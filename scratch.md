#APIs
namespace app/http/controllers/api [root api namespace]

Always version the API
any big changes get a new version

namespace app/http/controllers/api/v1
namespace app/http/controllers/api/v2

Need to handle different versions of the API in routes

Hvae a uniform response structure

validate validate validate

Responses in?
- JSON
- SOAP (XML)
- GraphQL


Installing the API requirements
php artisan install:api



If your route uses {category} and your controller type-hints Category $category, binding should work.
If it only works when you use $id, it means the binding fails because Laravel cannot find a matching record — usually due to:

The route parameter name being different ({id} vs $category)

Database visibility in tests (transactions / in-memory DB)

Or some middleware / trait affecting queries


$user = User::factory()->create();
$this->actingAs($user, 'sanctum');