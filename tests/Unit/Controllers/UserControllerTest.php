<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\UserController;
use App\Http\Requests\Programs\ProgramsRequest;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Jobs\GPTSeeder\UsersFill;
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

class UserControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

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

        $users = User::factory()->count($count)->create();

        $this->assertCount($count, $users);
        
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

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
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = $controller->store(
            StoreUserRequest::create('/api/users', 'POST', [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
                'image_path' => $this->faker->imageUrl(),
            ]),
            ProgramsRequest::create('/api/users', 'POST', []),
        );

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $controller->store(
            StoreUserRequest::create('/api/users', 'POST', [
                'email' => $this->faker->email,
                'image_path' => $this->faker->imageUrl(),
            ]),
            ProgramsRequest::create('/api/users', 'POST', []),
        );
    }

    /**
     * Test create model without email
     */
    public function test_create_without_email(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $controller->store(
            StoreUserRequest::create('/api/users', 'POST', [
                'name' => $this->faker->name,
                'image_path' => $this->faker->imageUrl(),
            ]),
            ProgramsRequest::create('/api/users', 'POST', []),
        );
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = $controller->store(
            StoreUserRequest::create('/api/users', 'POST', [
                'name' => $this->faker->name,
                'email' => $this->faker->email,
            ]),
            ProgramsRequest::create('/api/users', 'POST', []),
        );

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $foundUser = $controller->show($user->getKey());

        $this->assertInstanceOf(User::class, $foundUser);

        $this->assertTrue($user->is($foundUser));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $controller->show($this->faker->numberBetween());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $request = UpdateUserRequest::create("/api/users/{$user->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedUser = $controller->update(
            $request,
            ProgramsRequest::create("/api/users/{$user->getKey()}", 'PATCH', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals($newAttributes, [
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'image_path' => $updatedUser->image_path,
        ]);
    }

    /**
     * Test update model without name
     */
    public function test_update_without_name(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $request = UpdateUserRequest::create("/api/users/{$user->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedUser = $controller->update(
            $request,
            ProgramsRequest::create("/api/users/{$user->getKey()}", 'PATCH', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
            array_merge([
                'name' => $user->name,
            ], $newAttributes),
            [
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'image_path' => $updatedUser->image_path,
            ]
        );
    }

    /**
     * Test update model without email
     */
    public function test_update_without_email(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'image_path' => $this->faker->imageUrl(),
        ];

        $request = UpdateUserRequest::create("/api/users/{$user->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedUser = $controller->update(
            $request,
            ProgramsRequest::create("/api/users/{$user->getKey()}", 'PATCH', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
            array_merge([
                'email' => $user->email,
            ], $newAttributes),
            [
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'image_path' => $updatedUser->image_path,
            ]
        );
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $request = UpdateUserRequest::create("/api/users/{$user->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedUser = $controller->update(
            $request,
            ProgramsRequest::create("/api/users/{$user->getKey()}", 'PATCH', []),
            $user->getKey(),
        );

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
            array_merge([
                'image_path' => $user->image_path,
            ], $newAttributes),
            [
                'name' => $updatedUser->name,
                'email' => $updatedUser->email,
                'image_path' => $updatedUser->image_path,
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
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $fakeId = $this->faker->randomNumber();

        $request = UpdateUserRequest::create("/api/users/{$fakeId}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $controller->update(
            $request,
            ProgramsRequest::create("/api/users/{$fakeId}", 'PATCH', []),
            $fakeId,
        );
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        /**
         * @var JsonResponse $deleteResult
         */
        $deleteResult = $controller->destroy($user->getKey());

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
         * @var UserController $controller
         */
        $controller = app(UserController::class);

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
                            'content' => "{ \"users\": [ { \"name\": \"John Doe\", \"email\": \"johndoe@example.com\", \"image_path\": \"https://example.com/images/johndoe.jpg\" }, { \"name\": \"Jane Smith\", \"email\": \"janesmith@example.com\", \"image_path\": \"https://example.com/images/janesmith.jpg\" }, { \"name\": \"Mike Johnson\", \"email\": \"mikejohnson@example.com\", \"image_path\": \"https://example.com/images/mikejohnson.jpg\" }, { \"name\": \"Emily Brown\", \"email\": \"emilybrown@example.com\", \"image_path\": \"https://example.com/images/emilybrown.jpg\" }, { \"name\": \"David Wilson\", \"email\": \"davidwilson@example.com\", \"image_path\": \"https://example.com/images/davidwilson.jpg\" }, { \"name\": \"Sarah Miller\", \"email\": \"sarahmiller@example.com\", \"image_path\": \"https://example.com/images/sarahmiller.jpg\" }, { \"name\": \"Chris Martinez\", \"email\": \"chrismartinez@example.com\", \"image_path\": \"https://example.com/images/chrismartinez.jpg\" }, { \"name\": \"Amy Lee\", \"email\": \"amylee@example.com\", \"image_path\": \"https://example.com/images/amylee.jpg\" }, { \"name\": \"Kevin Nguyen\", \"email\": \"kevinnguyen@example.com\", \"image_path\": \"https://example.com/images/kevinnguyen.jpg\" }, { \"name\": \"Jessica Taylor\", \"email\": \"jessicataylor@example.com\", \"image_path\": \"https://example.com/images/jessicataylor.jpg\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);
        
        /**
         * @var UserController $controller
         */
        $controller = app(UserController::class);

        $response = $controller->gpt();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertTrue($response->getData(true)['success']);
        
        Bus::assertDispatched(UsersFill::class);
    }
}
