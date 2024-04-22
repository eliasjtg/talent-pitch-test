<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ChallengeController;
use App\Http\Requests\Challenges\StoreChallengeRequest;
use App\Http\Requests\Challenges\UpdateChallengeRequest;
use App\Models\Challenge;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
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
                'user_id' => $user->getKey(),
                'title' => fake()->text(20),
                'description' => fake()->realText(),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
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
                'user_id' => $user->getKey(),
                'description' => fake()->realText(),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
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
                'user_id' => $user->getKey(),
                'title' => fake()->text(20),
                'difficulty' => fake()->numberBetween(1, 10),
            ]),
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
                'user_id' => $user->getKey(),
                'title' => fake()->text(20),
                'description' => fake()->realText(),
            ]),
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
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $request = UpdateChallengeRequest::create("/api/challenges/{$challenge->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedChallenge = $controller->update(
            $request,
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
}
