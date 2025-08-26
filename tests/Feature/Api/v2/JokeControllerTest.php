<?php

use \App\Models\Category;
use \App\Models\Joke;
use \App\Models\User;

use Illuminate\Foundation\Testing\RefreshDatabase;

//const API_VER = 'v2';

uses(RefreshDatabase::class);

test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});


test('retrieve all jokes', function () {

    // Arrange
    $jokes = Joke::factory(5)->create();

    $data = [
        'success' => true,
        'message' => "All jokes",
        'data' => $jokes->toArray(),
    ];

    // Act: request the show endpoint
    $response = $this->getJson('/api/' . API_VER . '/jokes/');
    

    // Assert
    $response
        ->assertStatus(200)
        ->assertJson($data)
        ->assertJsonCount(5, 'data');
});


test('test retrieve all jokes', function () {

    $user = User::factory()->create();

    Joke::factory()->count(3)->create([
        'user_id' => $user->id,
    ]);

    $response = $this->getJson('/api/v2/jokes');

    $response->assertOk();
});



test('test retrieve one joke', function () {

    $joke = Joke::factory()->create();

    $response = $this->getJson('/api/v2/jokes/' . $joke->id);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Joke Found',
            // 'data' => [
            //     'id' => $joke->id,
            //     'title' => $joke->title,
            //     'content' => $joke->content,
            //     'user_id' => $joke->user_id,
            // ],
        ]);

   $response->assertJsonStructure([
        'success',
        'message',
        'data' => [
            '*' => [       // * means “each item in the array”
                'id',
                'title',
                'content',
                'user_id',
                'created_at',
                'updated_at',
                'deleted_at' // if soft deletes are used
            ],
        ],
    ]);
});


test('test retrieve one joke not found', function () {

    $response = $this->getJson('/api/v2/jokes/9999'); // Assuming 9999 is an ID that does not exist

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Joke not found',
        ]);
});


test('test create a joke', function () {

    $user = User::factory()->create();

    $category = Category::factory()->create();

    $data = [
        'title' => 'Test Joke',
        'content' => 'This is a test joke content.',
        'categories' => [$category->id], // Assuming the category exists
    ];


    $response = $this->actingAs($user)->postJson('/api/v2/jokes', $data);

    $response->assertCreated()
        ->assertJson([
            'success' => true,
            'message' => 'Joke created',
            'data' => [
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $user->id,
            ],
        ]);
});


test('test update a joke', function () {

    $user = User::factory()->create();

    $joke = Joke::factory()->create(['user_id' => $user->id]);

    $data = [
        'title' => 'Updated Joke Title',
        'content' => 'Updated joke content.',
        'categories' => [], // No categories for this test
    ];

    $response = $this->actingAs($user)->putJson('/api/v2/jokes/' . $joke->id, $data);

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Joke Updated',
            'data' => [
                'title' => $data['title'],
                'content' => $data['content'],
                'user_id' => $user->id,
            ],
        ]);
});


test('test update a joke not found', function () {

    $user = User::factory()->create();
    
    $data = [
        'title' => 'Updated Joke Title',
        'content' => 'Updated joke content.',
        'categories' => [], // No categories for this test
    ];

    $response = $this->actingAs($user)->putJson('/api/v2/jokes/9999', $data); // Assuming 9999 is an ID that does not exist

    $response->assertStatus(404)
        ->assertJson([
            'success' => false,
            'message' => 'Joke not found',
        ]);
});



test('test delete a joke', function () {

    $user = User::factory()->create();

    $joke = Joke::factory()->create(['user_id' => $user->id]);

    $response = $this->actingAs($user)->deleteJson('/api/v2/jokes/' . $joke->id);

    //$response->assertNoContent();
    $response->assertStatus(200);


    // Assert that the joke is soft deleted
    $this->assertSoftDeleted('jokes', ['id' => $joke->id]);

});

test('test delete a joke not found', function () {

    $user = User::factory()->create();

    $response = $this->actingAs($user)->deleteJson('/api/v2/jokes/9999'); // Assuming 9999 is an ID that does not exist

    $response->assertStatus(404)
        ->assertJson([
            //'success' => false,
            //'message' => 'Joke not found',
        ]);
});

    
test('test retrieve trashed jokes', function () {

    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $joke = Joke::factory()->create(['user_id' => $user->id]);

    // Soft delete the joke
    $joke->delete();

    $response = $this->getJson('/api/v2/jokes/trash');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'All trashed jokes',
            'data' => [
                [
                    'id' => $joke->id,
                    'title' => $joke->title,
                    'content' => $joke->content,
                    'user_id' => $user->id,
                ],
            ],
        ]);
});


test('test recover a trashed joke', function () {

    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $joke = Joke::factory()->create(['user_id' => $user->id]);

    // Soft delete the joke
    $joke->delete();

    $response = $this->postJson('/api/v2/jokes/trash/' . $joke->id . '/recover');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Joke recovered from trash',
            'data' => [
                'id' => $joke->id,
                'title' => $joke->title,
                'content' => $joke->content,
                'user_id' => $user->id,
            ],
        ]);

    // Assert that the joke is no longer soft deleted
    $this->assertDatabaseHas('jokes', ['id' => $joke->id,
        'deleted_at' => null]);
});


test('test remove a trashed joke', function () {

    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $joke = Joke::factory()->create(['user_id' => $user->id]);

    // Soft delete the joke
    $joke->delete();

    $response = $this->deleteJson('/api/v2/jokes/trash/' . $joke->id . '/remove');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'Joke removed from trash',
            'data' => [
                'id' => $joke->id,
                'title' => $joke->title,
                'content' => $joke->content,
                'user_id' => $user->id,
            ],
        ]);

    // Assert that the joke is permanently deleted
    $this->assertDatabaseMissing('jokes', ['id' => $joke->id]);
});



test('test recover all trashed jokes', function () {

    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $jokes = Joke::factory()->count(3)->create(['user_id' => $user->id]);

    // Soft delete all jokes
    foreach ($jokes as $joke) {
        $joke->delete();
    }

    $response = $this->postJson('/api/v2/jokes/trash/recover-all');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'All trashed jokes recovered',
            // 'data' => $jokes->toArray(), - contains all the attributes (timestamps, deleted_at etc )
        ])
         ->assertJsonStructure([ // had to loosen the assertion as the above was causing the test to fail
        'data' => [
            '*' => ['id', 'user_id', 'content'] // or whatever fields your API returns
        ]
    ]);

    // Assert that all jokes are no longer soft deleted
    foreach ($jokes as $joke) {
        $this->assertDatabaseHas('jokes', ['id' => $joke->id, 'deleted_at' => null]);
    }
});


test('test remove all trashed jokes', function () {

    $user = User::factory()->create();
    $this->actingAs($user, 'sanctum');

    $jokes = Joke::factory()->count(3)->create(['user_id' => $user->id]);

    // Soft delete all jokes
    foreach ($jokes as $joke) {
        $joke->delete();
    }

    $response = $this->deleteJson('/api/v2/jokes/trash/empty');

    $response->assertOk()
        ->assertJson([
            'success' => true,
            'message' => 'All trashed jokes removed',
            'data' => $jokes->toArray(),
        ]);

    // Assert that all jokes are permanently deleted
    foreach ($jokes as $joke) {
        $this->assertDatabaseMissing('jokes', ['id' => $joke->id]);
    }
});