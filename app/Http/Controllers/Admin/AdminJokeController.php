<?php
/**
 * Assessment Title: Portfolio Project
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * JokeController.php
 *
 *Joke Controller with methods for managing basic BREAD operations (browse, read, add, edit & delete) plus search and soft deletes.
 *
 * Filename:        JokeController.php
 * Location:        /App/Web/controllers
 * Project:         jokes-api-2025-s2
 * Date Created:    19/08/2025
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 

//set up namespace
namespace App\Http\Controllers\Admin;

// import dependencies
use App\Models\Joke;
use App\Http\Requests\StoreJokeRequest;
use App\Http\Requests\UpdateJokeRequest;
use App\Http\Controllers\Controller;

//search
use Illuminate\Http\Request;

//trash methods
use Illuminate\Http\RedirectResponse;

//categories
use App\Models\Category;

// to use the authorize() method
use Illuminate\Foundation\Auth\Access\AuthorizesRequests; // ✅ Add this

class AdminJokeController extends Controller
{
    //use AuthorizesRequests; // ✅ Add this if it's missing
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    // index method to list all jokes with search and category filter
    // and pagination
    


public function index(Request $request)
{
     //$this->authorize('jokes.index');

     // get search terms
    $validated = $request->validate([
        'search' => 'nullable|string|max:25',
        'category' => 'nullable|integer|exists:categories,id',
    ]);

    $searchTerm = $validated['search'] ?? null;
    $categoryId = $validated['category'] ?? null;

    // query the database for the search term or category
    $jokes = Joke::with(['user', 'categories'])
        ->when($searchTerm, function ($query, $searchTerm) {
            $query->where(function ($q) use ($searchTerm) {
                $q->where('title', 'like', '%' . $searchTerm . '%')
                  ->orWhere('body', 'like', '%' . $searchTerm . '%');
            });
        })
        ->when($categoryId, function ($query, $categoryId) {
            $query->whereHas('categories', function ($q) use ($categoryId) {
                $q->where('categories.id', $categoryId);
            });
        })
        ->orderBy('id', 'desc')
        ->paginate(10)
        ->appends($validated);
       

    $categories = Category::all(); // For the dropdown

     $trashCount = Joke::onlyTrashed()->count();

    return view('admin.jokes.index', compact('jokes', 'categories','trashCount', 'searchTerm', 'categoryId'))
        ->with('success', 'Jokes found');
}



    /**
     * Show the form for creating a new resource.
     */
    public function create()  
    {
        // check that the user is authorized to create a joke
      //  $this->authorize('create',Joke::class);

        // get all categories for the dropdown
        $categories= Category::get();

        // return the view 
          return view('admin.jokes.create', compact(['categories']));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         // check that the user is authorized to create a joke
       //  $this->authorize('create',Joke::class);

        // validate post data
        $validated = $request->validate([
            'title' => ['required', 'min:1', 'max:255', 'string'],
        'content' => ['required', 'min:1',   'string'],
//                   'category_id' => ['required'], not requires as the category-joke relationship is stored in pivot table
           
        ]);

        // Add the user ID to the validated data
    $validated['user_id'] = auth()->id();

    // strip the htl tags from the input before storing in database
    $validated['content'] = strip_tags($validated['content']);

    // Create the new joke
    $joke = Joke::create($validated);


    
    // Attach selected categories to the joke
    $joke->categories()->attach($request->categories);

     
        return redirect(route('admin.jokes.index'))
            ->with('success', 'Joke created');


    }

    /**
     * Display the specified resource.
     */
    public function show(Joke $joke) //DONE
    {
        
        $joke->load('categories'); // eager load the related category
        $categories = $joke->categories; // get the categories for the joke

         return view('admin.jokes.show', compact(['joke','categories']))
                ->with('success', 'Joke found');
        
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Joke $joke)
    {
         // check that the user is authorized to edit a joke
       // $this->authorize('update', $joke);

        // get all categories for the dropdown
        $categories= Category::get();

        // return the view
        return view('admin.jokes.edit', compact(['joke', 'categories']));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Joke $joke)
    {
         // check that the user is authorized to update a joke
         // $this->authorize('update', $joke);
       
        // Validate the incoming data
            $validated = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string|min:16',
                 'categories' => 'array|exists:categories,id'
                //'category_id' =>'required|integer|exists:categories,id',  // Example validation for category

            ]);

     // strip the html tags from the input before storing in database
        $validated['content'] =  strip_tags($validated['content']);

        // Update the joke- use this instead of all of the above
        $joke->update($validated);

          // Sync the categories (many-to-many)
        $joke->categories()->sync($validated['categories'] ?? []);

        // Redirect or return response
        return redirect()->route('admin.jokes.index')->with('success', 'Joke updated successfully!');

    }


    public function delete(Joke $joke)
    {
         // check that the user is authorized to delete a joke
       // $this->authorize('delete', $joke);
        return(view('admin.jokes.delete', compact(['joke'])));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Joke $joke)
    {
         // check that the user is authorized to delete a joke
        // $this->authorize('delete', $joke);
         $joke->delete();
        return redirect(route('admin.jokes.index'))
         ->withSuccess("Joke No. {$joke->id} moved to trash.");
    }



    // Soft deletes
    // See https://github.com/AdyGCode/laravel11-bootcamp-2024-s1/blob/main/app/Http/Controllers/UserController.php

     public function trash()
    {
        $jokes = Joke::onlyTrashed()->orderBy('deleted_at')->paginate(5);
        return view('admin.jokes.trash', compact(['jokes',]));
    }

    // recoverOne 
    public function restore(string $id): RedirectResponse
    {
        $joke = Joke::onlyTrashed()->find($id);
        $joke->restore();
        return redirect()
            ->back()
            ->withSuccess("Restored joke with id {$joke->id}.");

    }

    // removeOne
    public function removeOne(string $id): RedirectResponse
    {
        $joke = Joke::onlyTrashed()->find($id);
        $oldJoke = $joke;
        $joke->forceDelete();
        return redirect()
            ->back()
            ->withSuccess("Permanently Removed joke no. : {$oldJoke->id}.");
    }

       /**
     * Restore all users in the trash to system
     *
     * @return RedirectResponse
     */
    public function recoverAll(): RedirectResponse
    {
        $jokes = Joke::onlyTrashed()->get();
        $trashCount = $jokes->count();

        foreach ($jokes as $joke) {
            $joke->restore(); // This restores the soft-deleted user
        }
        return redirect(route('admin.jokes.index'))
            ->withSuccess("Successfully recovered {$trashCount} jokes.");
    }

    /**
     * Permanently remove all jokes that are in the trash ie emptyAll
     *
     * @return RedirectResponse
     */
    public function removeAll(): RedirectResponse
    {
        $jokes = Joke::onlyTrashed()->get();
        $trashCount = $jokes->count();
        foreach ($jokes as $joke) {
            $joke->forceDelete(); // This removes the soft-deleted user
        }
        return redirect(route('admin.jokes.trash'))
            ->withSuccess("Successfully emptied trash of {$trashCount} jokes.");
    }

}
