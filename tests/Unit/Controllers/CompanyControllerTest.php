<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\CompanyController;
use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use App\Http\Requests\Programs\ProgramsRequest;
use App\Jobs\GPTSeeder\CompaniesFill;
use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\Route;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class CompanyControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $pagination = $controller->index();

        $this->assertInstanceOf(LengthAwarePaginator::class, $pagination);

        $this->assertEquals(0, $pagination->count());
    }

    /**
     * Test pagination
     */
    public function test_pagination(): void
    {
        $count = $this->faker->numberBetween(1, 10);

        $companies = Company::factory()->count($count)->create();

        $this->assertCount($count, $companies);
        
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $pagination = $controller->index();

        $this->assertInstanceOf(LengthAwarePaginator::class, $pagination);

        $this->assertEquals($count, $pagination->count());
    }

    /**
     * Test create model
     */
    public function test_create(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $company = $controller->store(
            StoreCompanyRequest::create('/api/companies', 'POST', [
                'name' => fake()->name,
                'image_path' => fake()->imageUrl(),
                'location' => fake()->country(),
                'industry' => fake()->company(),
            ]),
            ProgramsRequest::create('/api/companies', 'POST', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreCompanyRequest::create('/api/companies', 'POST', [
                'image_path' => fake()->imageUrl(),
                'location' => fake()->country(),
                'industry' => fake()->company(),
            ]),
            ProgramsRequest::create('/api/companies', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $company = $controller->store(
            StoreCompanyRequest::create('/api/companies', 'POST', [
                'name' => fake()->name,
                'location' => fake()->country(),
                'industry' => fake()->company(),
            ]),
            ProgramsRequest::create('/api/companies', 'POST', []),
            $user->getKey()
        );

        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * Test create model without location
     */
    public function test_create_without_location(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreCompanyRequest::create('/api/companies', 'POST', [
                'name' => fake()->name,
                'image_path' => fake()->imageUrl(),
                'industry' => fake()->company(),
            ]),
            ProgramsRequest::create('/api/companies', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test create model without industry
     */
    public function test_create_without_industry(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreCompanyRequest::create('/api/companies', 'POST', [
                'name' => fake()->name,
                'image_path' => fake()->imageUrl(),
                'location' => fake()->country(),
            ]),
            ProgramsRequest::create('/api/companies', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $foundCompany = $controller->show($company->getKey());

        $this->assertInstanceOf(Company::class, $foundCompany);

        $this->assertTrue($company->is($foundCompany));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $controller->show($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $request = UpdateCompanyRequest::create("/api/companies/{$company->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedCompany = $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$company->getKey()}", 'PATCH', []),
            $company->getKey(),
        );

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals($newAttributes, [
            'name' => $updatedCompany->name,
            'image_path' => $updatedCompany->image_path,
            'location' => $updatedCompany->location,
            'industry' => $updatedCompany->industry,
        ]);
    }

    /**
     * Test update model without name
     */
    public function test_update_without_name(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $request = UpdateCompanyRequest::create("/api/companies/{$company->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedCompany = $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$company->getKey()}", 'PATCH', []),
            $company->getKey(),
        );

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'name' => $company->name,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]
        );
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'name' => fake()->name,
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $request = UpdateCompanyRequest::create("/api/companies/{$company->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedCompany = $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$company->getKey()}", 'PATCH', []),
            $company->getKey(),
        );

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'image_path' => $company->image_path,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]
        );
    }

    /**
     * Test update model without location
     */
    public function test_update_without_location(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'industry' => fake()->company(),
        ];

        $request = UpdateCompanyRequest::create("/api/companies/{$company->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedCompany = $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$company->getKey()}", 'PATCH', []),
            $company->getKey(),
        );

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'location' => $company->location,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]
        );
    }

    /**
     * Test update model without industry
     */
    public function test_update_without_industry(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
        ];

        $request = UpdateCompanyRequest::create("/api/companies/{$company->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedCompany = $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$company->getKey()}", 'PATCH', []),
            $company->getKey(),
        );

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'industry' => $company->industry,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]
        );
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $fakeId = $this->faker->randomNumber();

        $request = UpdateCompanyRequest::create("/api/companies/{$fakeId}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $controller->update(
            $request,
            ProgramsRequest::create("/api/companies/{$fakeId}", 'PATCH', []),
            $fakeId,
        );
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        /**
         * @var JsonResponse $deleteResult
         */
        $deleteResult = $controller->destroy($company->getKey());

        $this->assertInstanceOf(JsonResponse::class, $deleteResult);

        $this->assertTrue($deleteResult->getData(true)['success']);
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $controller->destroy($this->faker->randomNumber());
    }

    /**
     * Test fill with gpt
     */
    public function test_fill_with_gpt(): void
    {
        Bus::fake();

        OpenAI::fake([
            CreateResponse::fake([
                'choices' => [
                    [
                        'message' => [
                            'content' => "{ \"companies\": [ { \"name\": \"Innovatech Solutions\", \"image_path\": \"https://example.com/images/innovatech_logo.png\", \"location\": \"New York, USA\", \"industry\": \"Information Technology\" }, { \"name\": \"GlobeTech Enterprises\", \"image_path\": \"https://example.com/images/globetech_logo.png\", \"location\": \"London, UK\", \"industry\": \"Telecommunications\" }, { \"name\": \"Vivid Dynamics Inc.\", \"image_path\": \"https://example.com/images/vividdynamics_logo.png\", \"location\": \"Los Angeles, USA\", \"industry\": \"Entertainment\" }, { \"name\": \"Apex Innovations Co.\", \"image_path\": \"https://example.com/images/apexinnovations_logo.png\", \"location\": \"Tokyo, Japan\", \"industry\": \"Consumer Electronics\" }, { \"name\": \"Meridian Solutions Ltd.\", \"image_path\": \"https://example.com/images/meridiansolutions_logo.png\", \"location\": \"Sydney, Australia\", \"industry\": \"Financial Services\" }, { \"name\": \"TechNova Industries\", \"image_path\": \"https://example.com/images/technova_logo.png\", \"location\": \"Berlin, Germany\", \"industry\": \"Software Development\" }, { \"name\": \"Fusion Innovations Group\", \"image_path\": \"https://example.com/images/fusioninnovations_logo.png\", \"location\": \"Toronto, Canada\", \"industry\": \"Renewable Energy\" }, { \"name\": \"AlphaWave Technologies\", \"image_path\": \"https://example.com/images/alphawave_logo.png\", \"location\": \"Seoul, South Korea\", \"industry\": \"Artificial Intelligence\" }, { \"name\": \"Astra Global Solutions\", \"image_path\": \"https://example.com/images/astraglobal_logo.png\", \"location\": \"Paris, France\", \"industry\": \"Aerospace\" }, { \"name\": \"NexGen Innovations\", \"image_path\": \"https://example.com/images/nexgen_logo.png\", \"location\": \"Shanghai, China\", \"industry\": \"Biotechnology\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);
        
        /**
         * @var CompanyController $controller
         */
        $controller = app(CompanyController::class);

        $response = $controller->gpt();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertTrue($response->getData(true)['success']);
        
        Bus::assertDispatched(CompaniesFill::class);
    }
}
