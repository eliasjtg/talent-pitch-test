<?php

namespace Tests\Feature;

use App\Jobs\GPTSeeder\ChallengesFill;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Bus;
use OpenAI\Laravel\Facades\OpenAI;
use OpenAI\Responses\Chat\CreateResponse;
use Tests\TestCase;

class ChallengesTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        $response = $this->getJson('/api/challenges');

        $response->assertStatus(200);

        $response->assertJsonCount(0, 'data');
    }

    /**
     * Test pagination
     */
    public function test_pagination(): void
    {
        $count = $this->faker->numberBetween(1, 10);

        $challenges = Challenge::factory()->count($count)->create();

        $this->assertCount($count, $challenges);
        
        $response = $this->getJson('/api/challenges');

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
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];
        
        $response = $this->postJson("/api/challenges/{$user->getKey()}", $attributes);

        $response->assertStatus(201);
    }

    /**
     * Test create model without title
     */
    public function test_create_without_title(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];
        
        $response = $this->postJson("/api/challenges/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without description
     */
    public function test_create_without_description(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'title' => fake()->text(20),
            'difficulty' => fake()->numberBetween(1, 10),
        ];
        
        $response = $this->postJson("/api/challenges/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test create model without difficulty
     */
    public function test_create_without_difficulty(): void
    {
        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $attributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
        ];
        
        $response = $this->postJson("/api/challenges/{$user->getKey()}", $attributes);

        $response->assertStatus(422);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);
        
        $response = $this->getJson("/api/challenges/{$challenge->getKey()}");

        $response->assertStatus(200);

        $this->assertEquals($challenge->getKey(), $response->json('id'));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $response = $this->getJson("/api/challenges/{$this->faker->randomNumber()}");

        $response->assertStatus(404);
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $response = $this->patchJson("/api/challenges/{$challenge->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals($newAttributes, [
            'user_id' => $response->json('user.id'),
            'title' => $response->json('title'),
            'description' => $response->json('description'),
            'difficulty' => $response->json('difficulty'),
        ]);
    }

    /**
     * Test update model without user_id
     */
    public function test_update_without_user_id(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $response = $this->patchJson("/api/challenges/{$challenge->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'user_id' => $challenge->user->getKey(),
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'title' => $response->json('title'),
                'description' => $response->json('description'),
                'difficulty' => $response->json('difficulty'),
            ]
        );
    }

    /**
     * Test update model without title
     */
    public function test_update_without_title(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $response = $this->patchJson("/api/challenges/{$challenge->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'title' => $challenge->title,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'title' => $response->json('title'),
                'description' => $response->json('description'),
                'difficulty' => $response->json('difficulty'),
            ]
        );
    }

    /**
     * Test update model without description
     */
    public function test_update_without_description(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'title' => fake()->text(20),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $response = $this->patchJson("/api/challenges/{$challenge->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'description' => $challenge->description,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'title' => $response->json('title'),
                'description' => $response->json('description'),
                'difficulty' => $response->json('difficulty'),
            ]
        );
    }

    /**
     * Test update model without difficulty
     */
    public function test_update_without_difficulty(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'user_id' => $user->getKey(),
            'title' => fake()->text(20),
            'description' => fake()->realText(),
        ];

        $response = $this->patchJson("/api/challenges/{$challenge->getKey()}", $newAttributes);

        $response->assertStatus(200);

        $this->assertEquals(
            array_merge([
                'difficulty' => $challenge->difficulty,
            ], $newAttributes),
            [
                'user_id' => $response->json('user.id'),
                'title' => $response->json('title'),
                'description' => $response->json('description'),
                'difficulty' => $response->json('difficulty'),
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
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $response = $this->patchJson("/api/challenges/{$this->faker->randomNumber()}", $newAttributes);

        $response->assertStatus(404);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);
        
        $response = $this->deleteJson("/api/challenges/{$challenge->getKey()}");

        $response->assertStatus(200);

        $this->assertTrue($response->json('success'));
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $response = $this->deleteJson("/api/challenges/{$this->faker->randomNumber()}");

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
                            'content' => "{ \"challenges\": [ { \"title\": \"Mountain Climbing Adventure\", \"description\": \"Conquer the tallest peak in the region\", \"difficulty\": 9 }, { \"title\": \"Survival Escape Room\", \"description\": \"Test your skills in a series of challenging puzzles\", \"difficulty\": 7 }, { \"title\": \"Cross-Country Cycling Challenge\", \"description\": \"Navigate rugged terrain on a long-distance bike ride\", \"difficulty\": 6 }, { \"title\": \"Wilderness Survival Camp\", \"description\": \"Learn to survive in the great outdoors without modern amenities\", \"difficulty\": 8 }, { \"title\": \"Underwater Scuba Adventure\", \"description\": \"Explore the depths of the ocean and encounter marine life\", \"difficulty\": 5 }, { \"title\": \"Parkour Urban Challenge\", \"description\": \"Navigate through urban obstacles with agility and speed\", \"difficulty\": 7 }, { \"title\": \"White Water Rafting Expedition\", \"description\": \"Conquer rapids and rough waters in a thrilling rafting trip\", \"difficulty\": 9 }, { \"title\": \"Extreme Skydiving Experience\", \"description\": \"Leap from a plane and freefall through the clouds\", \"difficulty\": 8 }, { \"title\": \"Rock Climbing Competition\", \"description\": \"Compete against other climbers in a challenging ascent\", \"difficulty\": 7 }, { \"title\": \"Kayaking Adventure Race\", \"description\": \"Race against the clock and other competitors in a fast-paced kayak race\", \"difficulty\": 6 } ] }",
                        ]
                    ],
                ],
            ]),
        ]);

        $response = $this->postJson('/api/challenges/gpt');

        $response->assertStatus(200);
        
        Bus::assertDispatched(ChallengesFill::class);
    }
}
