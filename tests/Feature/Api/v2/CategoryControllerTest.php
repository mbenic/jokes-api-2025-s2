<?php
use Illuminate\Foundation\Testing\RefreshDatabase;
//use Illuminate\Foundation\Testing\WithFaker;
use App\Models\Category;
use App\Models\User;
//use Illuminate\Support\Facades\Artisan; 

const API_VERSION = 'v2';
const ENDPOINT = 'categories';

 uses(RefreshDatabase::class);




test('can retrieve all categories', function () {
    // Create some categories using a factory
    $categories = Category::factory()->count(3)->create();

    $data = [

        'message' => 'All categories',
        'success' => true,
        'data' => $categories->toArray(),
    ];


    $response = $this->get('/api/' . API_VERSION . '/' . ENDPOINT);

    $response->assertStatus(200);
    $response->assertJsonStructure([
        'data' => [
            '*' => [
                'id',
                'title',
                'description',
                'created_at',
                'updated_at',
            ],
        ],
        'message',
    ]);
    $response->assertJson($data);
});   


test('can retrieve a single category', function () {
    // Create a category using a factory
    //you’re getting an Eloquent\Collection — not a single model — so it does not have an $id property.
    $category = Category::factory()->create();

    //dd($category->toArray());

    $data = [
        'success' => true,
        'message' => 'Category Found',
        'data' =>[$category->toArray()],
    ];

    $response = $this->get('/api/' . API_VERSION . '/' . ENDPOINT . '/' . $category->id);

    $response->assertStatus(200);
    $response->assertJson($data)
             ->assertJsonCount(1, 'data');
});



test('can create a new category', function () {
    // doesnt work because of the middleware
     $this->withoutMiddleware();
    $data = [
        'title' => 'New Category',
        'description' => 'This is a new category.',
    ];

    $response = $this->post('/api/' . API_VERSION . '/' . ENDPOINT, $data);

    //$response->assertStatus(201);
    $response->assertJsonStructure([
        'message',
        'success',
        'data' => [
            'id',
            'title',
            'description',
            'created_at',
            'updated_at',
        ],
        
    ]);

    $response->assertJson([
        'message' => 'Category created',
        'success' => true,
        'data' =>$data,
        ],
    );
});

// Adrian's test for creating a new category
// This test is similar to the one above , uses the withoutMiddleware() method to bypass authentication
// and checks the response structure and data.
test('Adrians create a new category', function () {
    // doesnt work without this code because of the middleware
     $this->withoutMiddleware();

    // Arrange
    $data = [
        'title' => 'Fake Category',
        'description' => 'Fake Category Description',
    ];
    $dataResponse = [
        'message' => "Category created",
        'success' => true,
        'data' => $data
    ];

    // Act
    $response = $this->postJson('/api/' . API_VERSION . '/categories', $data);

    // Assert
    $response
        ->assertStatus(201)
        ->assertJson($dataResponse)
        ->assertJsonCount(5, 'data');
});




test('can destroy category', function () {
    $this->withoutMiddleware();

    // Arrange: Create a category
    $category = Category::factory()->create();

    // dd([
    //     'id' => $category->id,
    //     'title' => $category->title,
    //     'description' => $category->description,
    // ]);
    
    // $data = [
    //     'message' => 'Category Deleted',
    //     'success' => true,
    //     'data' => [
    //         'id' => $category->id,
    //         'title' => $category->title,
    //         'description' => $category->description,
    //     ],
    // ];

    // Act: Call the DELETE endpoint
    $response = $this->deleteJson('/api/' . API_VERSION . '/' . ENDPOINT . '/' . $category->id);
     //dd($response->getContent());
//dd('/api/' . API_VERSION . '/' . ENDPOINT . '/' . $category->id);


    // Assert: Response is correct

    $response->assertJson([
                   'success' => true,
                   'message' => 'Category deleted successfully.',
                   'data' => [
                     'title' => $category->title,
                     'description' => $category->description,
                 ]
             ]);
   $response->assertStatus(200);

    // Assert: Database no longer has the record
    $this->assertSoftDeleted('categories', [
    'id' => $category->id,
]);
});


test('can soft delete category', function () {
    $this->withoutMiddleware();

    $category = Category::factory()->create();

    $response = $this->deleteJson('/api/v2/categories/' . $category->id);

    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'message' => 'Category deleted successfully.',
                 'data' => [
                     'title' => $category->title,
                     'description' => $category->description,
                 ],
             ]);

    // Assert soft deleted: record exists but has non-null deleted_at
    $this->assertSoftDeleted('categories', ['id' => $category->id]);
});


test('direct delete test', function () {
    $category = Category::factory()->create();

    $category->delete();

    $this->assertSoftDeleted('categories', ['id' => $category->id]);
});


test('return error on missing category', function () {
    $response = $this->getJson('/api/' . API_VERSION . '/' . ENDPOINT . '/9999');

    $response->assertStatus(404)
             ->assertJson([
                 'message' => 'Category not found',
                 'success' => false,
                 'data' => [],
             ]);
});




test('can update category', function () {


  $this->withoutMiddleware();

    // Arrange: Create a category
    $category = Category::factory()->create();

    // Data to update the category
    $data = [
        'title' => 'Updated Category',
        'description' => 'Updated Description',
    ];

    // Act: Call the PUT endpoint to update the category
   // $response = $this->putJson('/api/' . API_VERSION . '/' . ENDPOINT . '/' . $category->id, $data);

      $response = $this->putJson("/api/v2/categories/{$category->id}", $data);

    // Assert: Response is correct
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'message' => 'Category Updated',
                 'data' => array_merge($data, [
                     'id' => $category->id,
                     //'created_at' => $category->created_at->toISOString(),
                     //'updated_at' => now()->toISOString(),
                 ]),
             ]);

    // Assert: Database has the updated record
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'title' => $data['title'],
        'description' => $data['description'],
    ]);
});


test('fail can update category ', function () {

    $this->withoutMiddleware();

    // Arrange: Create a category
    $category = Category::factory()->create();

    // Data to update the category
    $data = [
        'title' => 'Updated Category',
        'description' => 'Updated Description',
    ];

    // Act: Call the PUT endpoint to update the category
    $response = $this->putJson('/api/' . API_VERSION . '/' . ENDPOINT . '/9999', $data);

    // Assert: Response is correct
    $response->assertStatus(404)
             ->assertJson([
                 'success' => false,
                 'message' => 'Category not found',
                 'data' => [],
             ]);
});

test('fail can delete category ', function () {
    $this->withoutMiddleware();
    // Act: Call the DELETE endpoint to delete a non-existing category
    $response = $this->deleteJson('/api/' . API_VERSION . '/' . ENDPOINT . '/9999');        
    // Assert: Response is correct
    $response->assertStatus(404);
              

    });


    test('can trash category ', function () {
     $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    
    // Arrange: Create a category
    $category = Category::factory()->create();   
    // Soft delete the category
    $category->delete();   
    // Act:
    $response = $this->getJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash');     
      
    // Assert: Response is correct
    $response->assertStatus(200)
         ->assertJson([
             'success' => true,
             'message' => 'All trashed categories',
         ]) //Assert only the key value pairs we care about
         ->assertJsonFragment([
             'id' => $category->id,
             'title' => $category->title,
             'description' => $category->description,
         ]);

        });


    
    test('can recover all from trash ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');  
    // Arrange: Create a category and soft delete it
    $category = Category::factory()->create();
    $category->delete();
    // Act: Call the recover all endpoint
    $response = $this->postJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/recover'); 
    // Assert: Response is correct
    $response->assertStatus(200)
             ->assertJson([
                 'success' => true,
                 'message' => 'All trashed categories recovered',
             ])
             ->assertJsonFragment([
                    'id' => $category->id,  
                    'title' => $category->title,
                    'description' => $category->description,
             ]);        

              });


test('can remove all from trash ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    // Arrange: Create a category and soft delete it
    $category = Category::factory()->create();
    $category->delete();
    // Act: Call the remove all endpoint
    $response = $this->deleteJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/empty'); 
    // Assert: Response is correct
    $response->assertStatus(200)
                ->assertJson([  
                    'success' => true,
                    'message' => 'All trashed categories removed',
                ]);                                 
    // Assert: Database no longer has the record
    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
        'deleted_at' => null, // Ensure the record is completely removed
    ]);
});


test('can remove one from trash ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    // Arrange: Create a category and soft delete it
    $category = Category::factory()->create();
    $category->delete();
    // Act: Call the remove one endpoint
    $response = $this->deleteJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/' . $category->id . '/remove'); 
    // Assert: Response is correct
    $response->assertStatus(200)
                ->assertJson([  
                    'success' => true,
                    'message' => 'Category removed from trash',
                    'data' => [
                        'id' => $category->id,
                        'title' => $category->title,
                        'description' => $category->description,
                    ],
                ]);                                 
    // Assert: Database no longer has the record
    $this->assertDatabaseMissing('categories', [
        'id' => $category->id,
        'deleted_at' => null, // Ensure the record is completely removed
    ]);
});


test('can remove one from trash with error ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    // Act: Call the remove one endpoint with a non-existing ID
    $response = $this->deleteJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/9999/remove'); 
    // Assert: Response is correct
    $response->assertStatus(404)
                ->assertJson([  
                    'success' => false,
                    'message' => 'Category not found in trash',
                    'data' => [],
                ]);                                 
});


test('can recover one from trash ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    // Arrange: Create a category and soft delete it
    $category = Category::factory()->create();
    $category->delete();
    // Act: Call the recover one endpoint
    $response = $this->postJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/' . $category->id . '/recover'); 
    // Assert: Response is correct
    $response->assertStatus(200)
                ->assertJson([  
                    'success' => true,
                    'message' => 'Category recovered from trash',
                    'data' => [
                        'id' => $category->id,
                        'title' => $category->title,
                        'description' => $category->description,
                    ],
                ]);                                 
    // Assert: Database has the record back
    $this->assertDatabaseHas('categories', [
        'id' => $category->id,
        'deleted_at' => null, // Ensure the record is restored
    ]);
});


test('can recover one from trash with error ', function () {
    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');
    // Act: Call the recover one endpoint with a non-existing ID
    $response = $this->postJson('/api/' . API_VERSION . '/' . ENDPOINT . '/trash/9999/recover'); 
    // Assert: Response is correct
    $response->assertStatus(404)
                ->assertJson([  
                    'success' => false,
                    'message' => 'Category not found in trash',
                    'data' => [],
                ]);                                 
});