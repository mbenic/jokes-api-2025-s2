<?php
/**
 * Assessment Title: Portfolio Project
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * JokeController.php
 *
 * Joke Controller with methods for managing basic API BREAD operations (browse, read, add, edit & delete) plus search and soft deletes.
 *
 * Filename:        JokeController.php
 * Location:        /App/Http/controllers/Api/v2
 * Project:         jokes-api-2025-s2
 * Date Created:    24/08/2025
 *
 * Author:          Adrian Gould <Adrian.Gould@nmtafe.wa.edu.au>
 *                  Maria Benic <j317578@tafe.wa.edu.au>
 */ 

namespace App\Http\Controllers\Api\v2;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Illuminate\Http\JsonResponse;

use App\Models\Joke;
 
use App\Responses\ApiResponse as ApiResponseClass;



class JokeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return JsonResponse
     */
    public function index()
    {
       $jokes = Joke::all();  
  
    if (count($jokes) > 0) {  
        return ApiResponseClass::success($jokes, "All jokes");  
    }  
  
    return ApiResponseClass::error($jokes, "No jokes Found", 404);  
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
      $validatedData = $request->validate([
            'title' => ['string', 'required', 'min:4'],
            'content' => ['string', 'nullable', 'min:6'],
        ]);


   

    // Check if categories are provided and validate them
    if ($request->has('categories')) {
        $request->validate([
            'categories' => 'array|exists:categories,id'
        ]);
    } else {
        $request->categories = []; // Default to an empty array if no categories are provided
    }
    
        // Add the user ID to the validated data
    $validatedData['user_id'] = auth()->id();

    // strip the htl tags from the input before storing in database
    $validatedData['content'] = strip_tags($validatedData['content']);


     $joke = Joke::create($validatedData);

    // Attach selected categories to the joke
     $joke->categories()->attach($request->categories);

    return ApiResponseClass::success($joke, "Joke created", 201); 

    }

    /**
     * Display the specified resource.
     * @param string $id
     * @return JsonResponse
     */
    public function show(string $id)
    {
        $joke = Joke::whereId($id)->get();

       if (count($joke) === 0) {
            return ApiResponseClass::error($joke, "Joke not found", 404);
        }
        return ApiResponseClass::success($joke, "Joke Found");
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function update(Request $request, string $id)
    {
        // Validate the request data

       $validatedData= $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string|max:1000',
            'categories' => 'array|exists:categories,id'
        ]);
        
        // this is because we have an $id as a parameter instead of a model $company
       // $Joke = Joke::findOrFail($id);
        $joke = Joke::find($id);

        if (!$joke) {
            return ApiResponseClass::sendResponse([], "Joke not found",false, 404);
        }

        $joke->update($validatedData);

          // Sync the categories (many-to-many)
        $joke->categories()->sync($validated['categories'] ?? []);


       return ApiResponseClass::success($joke, "Joke Updated");

    }

    /**
     * Remove the specified resource from storage.
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id)
    {
     $joke= Joke::findOrFail($id);
        $joke->delete(); // will only softdelete the record
        return ApiResponseClass::sendResponse($joke, "Joke deleted successfully.", 200); 
    }


    /**
     * Show all soft deleted Jokes
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trash(Request $request)
    {
        $trashedJokes = Joke::onlyTrashed()->get();

        if ($trashedJokes->isEmpty()) {
            return ApiResponseClass::error([], "No jokes in trash", 404);
        }

        return ApiResponseClass::success($trashedJokes, "All trashed jokes");
    }

    /**
     * Recover all soft deleted jokes from trash
     *
     * @return void 
     * @return JsonResponse
     */
    public function recoverAll()
    {
        $trashedJokes = Joke::onlyTrashed()->get();

        if ($trashedJokes->isEmpty()) {
            return ApiResponseClass::error([], "No jokes in trash to recover", 404);
        }

        foreach ($trashedJokes as $joke) {
            $joke->restore();
        }

        return ApiResponseClass::success($trashedJokes, "All trashed jokes recovered");
    }

    /**
     * Remove all soft deleted jokes from trash
     *
     * @return  jsonResponse
     * 
     */
    public function removeAll()
    {
        $trashedJokes = Joke::onlyTrashed()->get();

        if ($trashedJokes->isEmpty()) {
            return ApiResponseClass::error([], "No jokes in trash to remove", 404);
        }

        foreach ($trashedJokes as $joke) {
            $joke->forceDelete();
        }

        return ApiResponseClass::success($trashedJokes, "All trashed jokes removed");
    }

    /**
     * Recover specified soft deleted Joke from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function recoverOne(string $id)
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return ApiResponseClass::error([], "Joke not found in trash", 404);
        }

        $joke->restore();

        return ApiResponseClass::success($joke, "Joke recovered from trash");
    }

    
    /**
     * Remove specified soft deleted Joke from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function removeOne(string $id)
    {
        $joke = Joke::onlyTrashed()->find($id);

        if (!$joke) {
            return ApiResponseClass::error([], "Joke not found in trash", 404);
        }

        $joke->forceDelete();

        return ApiResponseClass::success($joke, "Joke removed from trash");
    }
}


