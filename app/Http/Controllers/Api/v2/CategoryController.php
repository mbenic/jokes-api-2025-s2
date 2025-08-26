<?php

/**
 * Assessment Title: Portfolio Project
 * Cluster:          SaaS: Back End Development ICT50220
 * Qualification:    ICT50220 Diploma of Information Technology (Back End Development)
 *
 * CategoryController.php
 *
 * Category Controller with methods for managing basic API BREAD operations (browse, read, add, edit & delete) plus search and soft deletes.
 *
 * Filename:        CategoryController.php
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
use App\Http\Requests\UpdateCategoryRequest;

use App\Models\Category;
 
use App\Responses\ApiResponse as ApiResponseClass;

class CategoryController extends Controller
{

/**  
  * Returns a list of the Categories. 
  * @return JsonResponse
  */
  public function index()  
  {  
    $categories = Category::all();  
  
    if (count($categories) > 0) {  
        return ApiResponseClass::success($categories, "All categories");  
    }  
  
    return ApiResponseClass::error($categories, "No categories Found", 404);  
  }


   /**
 * Create & Store a new Category resource.
 *
 * @param Request $request
 * @return JsonResponse
 */
public function store( Request $request)  
{  
   
        $validatedData = $request->validate([
            'title' => ['string', 'required', 'min:4'],
            'description' => ['string', 'nullable', 'min:6'],
        ]);


    $category = Category::create($validatedData);

    return ApiResponseClass::success($category, "Category created", 201);  
}

/**
 * Display the specified Category.
 *
 * @param string $id
 * @return JsonResponse
 */

   public function show(string $id)
    {
        $category = Category::whereId($id)->get();

       if (count($category) === 0) {
            return ApiResponseClass::error($category, "Category not found", 404);
        }
        return ApiResponseClass::success($category, "Category Found");
    }



 
    /**
     * Update the specified Category resource.
     *
     * @param  Request $request
     * @param Category string $id
     * @return JsonResponse
     */
    
public function update(  Request $request, $id ): JsonResponse
    {
        // Validate the request data

       $validatedData= $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);
        
       // $category = Category::findOrFail($id);  will throw 404 if not found
        $category = Category::find($id);

        if (!$category) {
            return ApiResponseClass::sendResponse([], "Category not found",false, 404);
        }

        $category->update($validatedData);


       return ApiResponseClass::success($category, "Category Updated");

        
    }

    /**
     * Delete the specified Category from storage.
     *
     * Will return success true, success message, and the category just removed.
     *
     * @param  Request $request
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(Request $request, string $id): JsonResponse
    {
        
        $category= Category::findOrFail($id);
        $category->delete(); // will only softdelete the record
        return ApiResponseClass::sendResponse($category, "Category deleted successfully.", 200); 
  
    }


    /**
     * Show all soft deleted Categories
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function trash(Request $request)
    {
        $trashedCategories = Category::onlyTrashed()->get();

        if ($trashedCategories->isEmpty()) {
            return ApiResponseClass::error([], "No categories in trash", 404);
        }

        return ApiResponseClass::success($trashedCategories, "All trashed categories");
    }

    /**
     * Recover all soft deleted categories from trash
     *
     * @return JsonResponse
     */
    public function recoverAll()
    {
        $trashedCategories = Category::onlyTrashed()->get();

        if ($trashedCategories->isEmpty()) {
            return ApiResponseClass::error([], "No categories in trash to recover", 404);
        }

        foreach ($trashedCategories as $category) {
            $category->restore();
        }

        return ApiResponseClass::success($trashedCategories, "All trashed categories recovered");
    }

    /**
     * Remove all soft deleted categories from trash
     *
     * @return jsonResponse
     */
    public function removeAll()
    {
        $trashedCategories = Category::onlyTrashed()->get();

        if ($trashedCategories->isEmpty()) {
            return ApiResponseClass::error([], "No categories in trash to remove", 404);
        }

        foreach ($trashedCategories as $category) {
            $category->forceDelete();
        }

        return ApiResponseClass::success($trashedCategories, "All trashed categories removed");
    }
    /**
     * Recover specified soft deleted category from trash
     *
     * @param string $id
     * @return JsonResponse
     */
    public function recoverOne(string $id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return ApiResponseClass::error([], "Category not found in trash", 404);
        }

        $category->restore();

        return ApiResponseClass::success($category, "Category recovered from trash");
    }
    /**
     * Remove specified soft deleted category from trash
     *
     * @param string $id
     * @return jsonResponse
     */
    public function removeOne(string $id)
    {
        $category = Category::onlyTrashed()->find($id);

        if (!$category) {
            return ApiResponseClass::error([], "Category not found in trash", 404);
        }

        $category->forceDelete();

        return ApiResponseClass::success($category, "Category removed from trash");
    }
}
