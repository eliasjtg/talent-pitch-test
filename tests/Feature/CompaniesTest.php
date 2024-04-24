<?php

namespace Tests\Feature;

use App\Jobs\GPTSeeder\CompaniesFill;
use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class CompaniesTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        $response = $this->getJson('/api/companies');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');
    }

    /**
     * Test pagination
     */
    public function test_pagination(): void
    {
        $count = $this->faker->numberBetween(1, 10);

        $companies = Company::factory()->count($count)->create();

        $this->assertCount($count, $companies);
        
        $response = $this->getJson('/api/companies');

        $response->assertStatus(200);

        $response->assertJsonCount($count, 'data');
    }

    /**
     * Test create model
     */
    public function test_create(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];
        
        $response = $this->postJson("/api/companies/{$user->getKey()}", $attributes);

        $response->assertStatus(201);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];
        
        $response = $this->postJson("/api/companies/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];
        
        $response = $this->postJson("/api/companies/{$user->getKey()}", $attributes);

        $response->assertStatus(201);
    }

    /**
     * Test create model without location
     */
    public function test_create_without_location(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'industry' => fake()->company(),
        ];
        
        $response = $this->postJson("/api/companies/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without industry
     */
    public function test_create_without_industry(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
        ];
        
        $response = $this->postJson("/api/companies/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);
        
        $response = $this->getJson("/api/companies/{$company->getKey()}");

        $response->assertStatus(200);

        $this->assertEquals($company->getKey(), $response->json('id'));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $response = $this->getJson("/api/companies/{$this->faker->randomNumber()}");

        $response->assertStatus(404);
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals($newAttributes, [
            'user_id' => $response->json('user.id'),
            'name' => $response->json('name'),
            'image_path' => $response->json('image_path'),
            'location' => $response->json('location'),
            'industry' => $response->json('industry'),
        ]);
    }

    /**
     * Test update model without user_id
     */
    public function test_update_without_user_id(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'user_id' => $company->user->getKey(),
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'name' => $response->json('name'),
                'image_path' => $response->json('image_path'),
                'location' => $response->json('location'),
                'industry' => $response->json('industry'),
            ]
        );
    }

    /**
     * Test update model without name
     */
    public function test_update_without_name(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'name' => $company->name,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'name' => $response->json('name'),
                'image_path' => $response->json('image_path'),
                'location' => $response->json('location'),
                'industry' => $response->json('industry'),
            ]
        );
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'name' => fake()->name,
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'image_path' => $company->image_path,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'name' => $response->json('name'),
                'image_path' => $response->json('image_path'),
                'location' => $response->json('location'),
                'industry' => $response->json('industry'),
            ]
        );
    }

    /**
     * Test update model without location
     */
    public function test_update_without_location(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'location' => $company->location,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'name' => $response->json('name'),
                'image_path' => $response->json('image_path'),
                'location' => $response->json('location'),
                'industry' => $response->json('industry'),
            ]
        );
    }

    /**
     * Test update model without industry
     */
    public function test_update_without_industry(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
        ];

        $response = $this->patchJson("/api/companies/{$company->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'industry' => $company->industry,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'name' => $response->json('name'),
                'image_path' => $response->json('image_path'),
                'location' => $response->json('location'),
                'industry' => $response->json('industry'),
            ]
        );
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $response = $this->patchJson("/api/companies/{$this->faker->randomNumber()}", $newAttributes);

        $response->assertStatus(404);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);
        
        $response = $this->deleteJson("/api/companies/{$company->getKey()}");

        $response->assertStatus(200);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $response = $this->deleteJson("/api/companies/{$this->faker->randomNumber()}");

        $response->assertStatus(404);
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

        $response = $this->postJson('/api/companies/gpt');

        $response->assertStatus(200);
        
        Bus::assertDispatched(CompaniesFill::class);
    }
}
