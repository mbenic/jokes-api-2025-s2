<?php
use Illuminate\Foundation\Testing\RefreshDatabase;  
use function Spatie\PestPluginTestTime\testTime;  

use App\Models\Category;
  
uses( RefreshDatabase::class);

it('can delete a company', function () {

   $category= Category::factory()->create();
     $this->withoutMiddleware();


   $response = $this->deleteJson("/api/v2/categories/". $category->id);

    $response->assertStatus(200)
         ->assertJson([
             'success' => true,
             'message' => 'Category deleted successfully.',
              'data' => [
                'id' => $category->id,
                'title' => $category->title,
                'description' => $category->description,
                // 'created_at' => $category->created_at->toISOString(),
                // 'updated_at' => $category->updated_at->toISOString(),
                ]
             ]);


       // its been softdeleted only

    $this->assertSoftDeleted('categories', ['id' => $category->id]);
       
              });     
