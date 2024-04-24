<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ChallengeController;
use App\Http\Requests\Challenges\StoreChallengeRequest;
use App\Http\Requests\Challenges\UpdateChallengeRequest;
use App\Http\Requests\Programs\ProgramsRequest;
use App\Jobs\GPTSeeder\ChallengesFill;
use App\Models\Challenge;
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

class ChallengeControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

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

        $challenges = Challenge::factory()->count($count)->create();

        $this->assertCount($count, $challenges);
        
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

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
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $challenge = $controller->store(
            StoreChallengeRequest::create('/api/challenges', 'POST', [
                'title' => fake()->text(20),
                'description' => fake()->realText(),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
            ProgramsRequest::create('/api/challenges', 'POST', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(Challenge::class, $challenge);
    }

    /**
     * Test create model without title
     */
    public function test_create_without_title(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreChallengeRequest::create('/api/challenges', 'POST', [
                'description' => fake()->realText(),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
            ProgramsRequest::create('/api/challenges', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test create model without description
     */
    public function test_create_without_description(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreChallengeRequest::create('/api/challenges', 'POST', [
                'title' => fake()->text(20),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
            ProgramsRequest::create('/api/challenges', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test create model without difficulty
     */
    public function test_create_without_difficulty(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreChallengeRequest::create('/api/challenges', 'POST', [
                'title' => fake()->text(20),
                'description' => fake()->realText(),
            ]),
            ProgramsRequest::create('/api/challenges', 'POST', []),
            $user->getKey()
        );
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $foundChallenge = $controller->show($challenge->getKey());

        $this->assertInstanceOf(Challenge::class, $foundChallenge);

        $this->assertTrue($challenge->is($foundChallenge));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $controller->show($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $request = UpdateChallengeRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedChallenge = $controller->update(
            $request,
            ProgramsRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', []),
            $challenge->getKey(),
        );

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals($newAttributes, [
            'title' => $updatedChallenge->title,
            'description' => $updatedChallenge->description,
            'difficulty' => $updatedChallenge->difficulty,
        ]);
    }

    /**
     * Test update model without title
     */
    public function test_update_without_title(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newAttributes = [
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $request = UpdateChallengeRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedChallenge = $controller->update(
            $request,
            ProgramsRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', []),
            $challenge->getKey(),
        );

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'title' => $challenge->title,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
            ]
        );
    }

    /**
     * Test update model without description
     */
    public function test_update_without_description(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newAttributes = [
            'title' => fake()->text(20),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $request = UpdateChallengeRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedChallenge = $controller->update(
            $request,
            ProgramsRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', []),
            $challenge->getKey(),
        );

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'description' => $challenge->description,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
            ]
        );
    }

    /**
     * Test update model without difficulty
     */
    public function test_update_without_difficulty(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
        ];

        $request = UpdateChallengeRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedChallenge = $controller->update(
            $request,
            ProgramsRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', []),
            $challenge->getKey(),
        );

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'difficulty' => $challenge->difficulty,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
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
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $fakeId = $this->faker->randomNumber();

        $request = UpdateChallengeRequest::create("/api/challenges/{$fakeId}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $controller->update(
            $request,
            ProgramsRequest::create("/api/challenges/{$fakeId}", 'PATCH', []),
            $fakeId,
        );
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        /**
         * @var JsonResponse $deleteResult
         */
        $deleteResult = $controller->destroy($challenge->getKey());

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
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

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
                            'content' => "{ \"challenges\": [ { \"title\": \"Mountain Climbing Adventure\", \"description\": \"Conquer the tallest peak in the region\", \"difficulty\": 9 }, { \"title\": \"Survival Escape Room\", \"description\": \"Test your skills in a series of challenging puzzles\", \"difficulty\": 7 }, { \"title\": \"Cross-Country Cycling Challenge\", \"description\": \"Navigate rugged terrain on a long-distance bike ride\", \"difficulty\": 6 }, { \"title\": \"Wilderness Survival Camp\", \"description\": \"Learn to survive in the great outdoors without modern amenities\", \"difficulty\": 8 }, { \"title\": \"Underwater Scuba Adventure\", \"description\": \"Explore the depths of the ocean and encounter marine life\", \"difficulty\": 5 }, { \"title\": \"Parkour Urban Challenge\", \"description\": \"Navigate through urban obstacles with agility and speed\", \"difficulty\": 7 }, { \"title\": \"White Water Rafting Expedition\", \"description\": \"Conquer rapids and rough waters in a thrilling rafting trip\", \"difficulty\": 9 }, { \"title\": \"Extreme Skydiving Experience\", \"description\": \"Leap from a plane and freefall through the clouds\", \"difficulty\": 8 }, { \"title\": \"Rock Climbing Competition\", \"description\": \"Compete against other climbers in a challenging ascent\", \"difficulty\": 7 }, { \"title\": \"Kayaking Adventure Race\", \"description\": \"Race against the clock and other competitors in a fast-paced kayak race\", \"difficulty\": 6 } ] }",
                        ]
                    ],
                ],
            ]),
        ]);
        
        /**
         * @var ChallengeController $controller
         */
        $controller = app(ChallengeController::class);

        $response = $controller->gpt();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertTrue($response->getData(true)['success']);
        
        Bus::assertDispatched(ChallengesFill::class);
    }
}
