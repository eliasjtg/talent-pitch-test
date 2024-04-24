<?php

namespace Tests\Feature;

use App\Jobs\GPTSeeder\UsersFill;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class UsersTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        $response = $this->getJson('/api/users');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');
    }

    /**
     * Test pagination
     */
    public function test_pagination(): void
    {
        $count = $this->faker->numberBetween(1, 10);

        $users = User::factory()->count($count)->create();

        $this->assertCount($count, $users);
        
        $response = $this->getJson('/api/users');

        $response->assertStatus(200);

        $response->assertJsonCount($count, 'data');
    }

    /**
     * Test create model
     */
    public function test_create(): void
    {
        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];
        
        $response = $this->postJson('/api/users', $attributes);

        $response->assertStatus(201);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $attributes = [
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];
        
        $response = $this->postJson('/api/users', $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without email
     */
    public function test_create_without_email(): void
    {
        $attributes = [
            'name' => $this->faker->name,
            'image_path' => $this->faker->imageUrl(),
        ];
        
        $response = $this->postJson('/api/users', $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        $attributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];
        
        $response = $this->postJson('/api/users', $attributes);

        $response->assertStatus(201);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        
        $response = $this->getJson("/api/users/{$user->getKey()}");

        $response->assertStatus(200);

        $this->assertEquals($user->getKey(), $response->json('id'));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $response = $this->getJson("/api/users/{$this->faker->randomNumber()}");

        $response->assertStatus(404);
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $response = $this->patchJson("/api/users/{$user->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals($newAttributes, [
            'name' => $response->json('name'),
            'email' => $response->json('email'),
            'image_path' => $response->json('image_path'),
        ]);
    }

    /**
     * Test update model without name
     */
    public function test_update_without_name(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $response = $this->patchJson("/api/users/{$user->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'name' => $user->name,
            ], $newAttributes),
            [
                'name' => $response->json('name'),
                'email' => $response->json('email'),
                'image_path' => $response->json('image_path'),
            ]
        );
    }

    /**
     * Test update model without email
     */
    public function test_update_without_email(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'image_path' => $this->faker->imageUrl(),
        ];

        $response = $this->patchJson("/api/users/{$user->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'email' => $user->email,
            ], $newAttributes),
            [
                'name' => $response->json('name'),
                'email' => $response->json('email'),
                'image_path' => $response->json('image_path'),
            ]
        );
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $response = $this->patchJson("/api/users/{$user->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'image_path' => $user->image_path,
            ], $newAttributes),
            [
                'name' => $response->json('name'),
                'email' => $response->json('email'),
                'image_path' => $response->json('image_path'),
            ]
        );
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $response = $this->patchJson("/api/users/{$this->faker->randomNumber()}", $newAttributes);

        $response->assertStatus(404);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);
        
        $response = $this->deleteJson("/api/users/{$user->getKey()}");

        $response->assertStatus(200);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $response = $this->deleteJson("/api/users/{$this->faker->randomNumber()}");

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
                            'content' => "{ \"users\": [ { \"name\": \"John Doe\", \"email\": \"johndoe@example.com\", \"image_path\": \"https://example.com/images/johndoe.jpg\" }, { \"name\": \"Jane Smith\", \"email\": \"janesmith@example.com\", \"image_path\": \"https://example.com/images/janesmith.jpg\" }, { \"name\": \"Mike Johnson\", \"email\": \"mikejohnson@example.com\", \"image_path\": \"https://example.com/images/mikejohnson.jpg\" }, { \"name\": \"Emily Brown\", \"email\": \"emilybrown@example.com\", \"image_path\": \"https://example.com/images/emilybrown.jpg\" }, { \"name\": \"David Wilson\", \"email\": \"davidwilson@example.com\", \"image_path\": \"https://example.com/images/davidwilson.jpg\" }, { \"name\": \"Sarah Miller\", \"email\": \"sarahmiller@example.com\", \"image_path\": \"https://example.com/images/sarahmiller.jpg\" }, { \"name\": \"Chris Martinez\", \"email\": \"chrismartinez@example.com\", \"image_path\": \"https://example.com/images/chrismartinez.jpg\" }, { \"name\": \"Amy Lee\", \"email\": \"amylee@example.com\", \"image_path\": \"https://example.com/images/amylee.jpg\" }, { \"name\": \"Kevin Nguyen\", \"email\": \"kevinnguyen@example.com\", \"image_path\": \"https://example.com/images/kevinnguyen.jpg\" }, { \"name\": \"Jessica Taylor\", \"email\": \"jessicataylor@example.com\", \"image_path\": \"https://example.com/images/jessicataylor.jpg\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson('/api/users/gpt');

        $response->assertStatus(200);
        
        Bus::assertDispatched(UsersFill::class);
    }
}
