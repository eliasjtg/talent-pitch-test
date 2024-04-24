<?php

namespace Tests\Unit\Controllers;

use App\Http\Controllers\ProgramController;
use App\Http\Requests\Programs\StoreProgramRequest;
use App\Http\Requests\Programs\UpdateProgramRequest;
use App\Jobs\GPTSeeder\ProgramsFill;
use App\Models\Program;
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

class ProgramControllerTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

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

        $programs = Program::factory()->count($count)->create();

        $this->assertCount($count, $programs);
        
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

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
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $program = $controller->store(
            StoreProgramRequest::create('/api/companies', 'POST', [
                'title' => fake()->title(),
                'description' => fake()->realText(),
                'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
                'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            ]),
            $user->getKey(),
        );

        $this->assertInstanceOf(Program::class, $program);
    }

    /**
     * Test create model without title
     */
    public function test_create_without_title(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreProgramRequest::create('/api/companies', 'POST', [
                'description' => fake()->realText(),
                'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
                'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            ]),
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
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $program = $controller->store(
            StoreProgramRequest::create('/api/companies', 'POST', [
                'title' => fake()->title(),
                'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
                'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            ]),
            $user->getKey()
        );

        $this->assertInstanceOf(Program::class, $program);
    }

    /**
     * Test create model without start_date
     */
    public function test_create_without_start_date(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreProgramRequest::create('/api/companies', 'POST', [
                'title' => fake()->title(),
                'description' => fake()->realText(),
                'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            ]),
            $user->getKey()
        );
    }

    /**
     * Test create model without end_date
     */
    public function test_create_without_end_date(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $controller->store(
            StoreProgramRequest::create('/api/companies', 'POST', [
                'title' => fake()->title(),
                'description' => fake()->realText(),
                'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            ]),
            $user->getKey()
        );
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $foundProgram = $controller->show($program->getKey());

        $this->assertInstanceOf(Program::class, $foundProgram);

        $this->assertTrue($program->is($foundProgram));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);

        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $controller->show($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $request = UpdateProgramRequest::create("/api/companies/{$program->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedProgram = $controller->update(
            $request,
            $program->getKey(),
        );

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals($newAttributes, [
            'title' => $updatedProgram->title,
            'description' => $updatedProgram->description,
            'start_date' => $updatedProgram->start_date,
            'end_date' => $updatedProgram->end_date,
        ]);
    }

    /**
     * Test update model without title
     */
    public function test_update_without_title(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newAttributes = [
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $request = UpdateProgramRequest::create("/api/companies/{$program->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedProgram = $controller->update(
            $request,
            $program->getKey(),
        );

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals(
            array_merge([
                'title' => $program->title,
            ], $newAttributes),
            [
                'title' => $updatedProgram->title,
                'description' => $updatedProgram->description,
                'start_date' => $updatedProgram->start_date,
                'end_date' => $updatedProgram->end_date,
            ]
        );
    }

    /**
     * Test update model without description
     */
    public function test_update_without_description(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newAttributes = [
            'title' => fake()->title(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $request = UpdateProgramRequest::create("/api/companies/{$program->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedProgram = $controller->update(
            $request,
            $program->getKey(),
        );

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals(
            array_merge([
                'description' => $program->description,
            ], $newAttributes),
            [
                'title' => $updatedProgram->title,
                'description' => $updatedProgram->description,
                'start_date' => $updatedProgram->start_date,
                'end_date' => $updatedProgram->end_date,
            ]
        );
    }

    /**
     * Test update model without start_date
     */
    public function test_update_without_start_date(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $request = UpdateProgramRequest::create("/api/companies/{$program->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedProgram = $controller->update(
            $request,
            $program->getKey(),
        );

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals(
            array_merge([
                'start_date' => $program->start_date,
            ], $newAttributes),
            [
                'title' => $updatedProgram->title,
                'description' => $updatedProgram->description,
                'start_date' => $updatedProgram->start_date,
                'end_date' => $updatedProgram->end_date,
            ]
        );
    }

    /**
     * Test update model without end_date
     */
    public function test_update_without_end_date(): void
    {
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $request = UpdateProgramRequest::create("/api/companies/{$program->getKey()}", 'PATCH', $newAttributes);

        $request->setRouteResolver(function () use ($request) {
            $routes = Route::getRoutes();
    
            return $routes->match($request);
        });

        $updatedProgram = $controller->update(
            $request,
            $program->getKey(),
        );

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals(
            array_merge([
                'end_date' => $program->end_date,
            ], $newAttributes),
            [
                'title' => $updatedProgram->title,
                'description' => $updatedProgram->description,
                'start_date' => $updatedProgram->start_date,
                'end_date' => $updatedProgram->end_date,
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
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $fakeId = $this->faker->randomNumber();

        $request = UpdateProgramRequest::create("/api/companies/{$fakeId}", 'PATCH', $newAttributes);

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
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        /**
         * @var JsonResponse $deleteResult
         */
        $deleteResult = $controller->destroy($program->getKey());

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
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

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
                            'content' => "{ \"programs\": [ { \"title\": \"Virtual Reality Workshop\", \"description\": \"Explore the exciting world of virtual reality technology in this hands-on workshop. Learn about VR hardware, software, and applications.\", \"start_date\": \"2024-05-10T09:00:00\", \"end_date\": \"2024-05-12T17:00:00\" }, { \"title\": \"Mobile App Development Bootcamp\", \"description\": \"Join us for an intensive bootcamp where you'll learn to develop mobile apps for iOS and Android platforms. No prior experience required!\", \"start_date\": \"2024-06-15T10:30:00\", \"end_date\": \"2024-06-20T16:00:00\" }, { \"title\": \"Data Science Masterclass\", \"description\": \"Unlock the power of data with our comprehensive masterclass. From data analysis to machine learning, we cover it all!\", \"start_date\": \"2024-07-08T13:00:00\", \"end_date\": \"2024-07-12T18:30:00\" }, { \"title\": \"Photography Workshop\", \"description\": \"Capture the world through your lens! Join our photography workshop to learn techniques for composition, lighting, and editing.\", \"start_date\": \"2024-08-03T11:00:00\", \"end_date\": \"2024-08-05T15:45:00\" }, { \"title\": \"Web Development Crash Course\", \"description\": \"Get up to speed with web development in this intensive crash course. Learn HTML, CSS, JavaScript, and more in just one week!\", \"start_date\": \"2024-09-20T09:30:00\", \"end_date\": \"2024-09-24T16:15:00\" }, { \"title\": \"Digital Marketing Seminar\", \"description\": \"Stay ahead in the digital age with our seminar on digital marketing strategies. From SEO to social media, we've got you covered!\", \"start_date\": \"2024-10-12T14:00:00\", \"end_date\": \"2024-10-13T18:00:00\" }, { \"title\": \"Creative Writing Workshop\", \"description\": \"Unleash your creativity with our interactive writing workshop. Explore various genres and techniques to craft compelling stories.\", \"start_date\": \"2024-11-05T10:00:00\", \"end_date\": \"2024-11-08T16:30:00\" }, { \"title\": \"UI/UX Design Bootcamp\", \"description\": \"Design intuitive and user-friendly interfaces in our UI/UX design bootcamp. Learn industry-standard tools and best practices.\", \"start_date\": \"2024-12-01T09:30:00\", \"end_date\": \"2024-12-05T17:00:00\" }, { \"title\": \"Blockchain Fundamentals Workshop\", \"description\": \"Dive into the world of blockchain technology and cryptocurrencies in this introductory workshop. Understand the basics and potential applications.\", \"start_date\": \"2025-01-10T13:30:00\", \"end_date\": \"2025-01-12T16:45:00\" }, { \"title\": \"Artificial Intelligence Summit\", \"description\": \"Join industry experts and researchers at our AI summit. Explore the latest advancements, trends, and ethical considerations in AI.\", \"start_date\": \"2025-02-20T10:00:00\", \"end_date\": \"2025-02-22T17:30:00\" } ] }",
                        ]
                    ],
                ],
            ]),
        ]);
        
        /**
         * @var ProgramController $controller
         */
        $controller = app(ProgramController::class);

        $response = $controller->gpt();

        $this->assertInstanceOf(JsonResponse::class, $response);

        $this->assertTrue($response->getData(true)['success']);
        
        Bus::assertDispatched(ProgramsFill::class);
    }
}
