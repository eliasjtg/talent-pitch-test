<?php

namespace Tests\Unit\Repositories;
use App\Models\Program;
use App\Models\User;
use App\Repositories\ProgramRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProgramRepositoryTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $pagination = $repository->paginate();

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
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $pagination = $repository->paginate();

        $this->assertInstanceOf(LengthAwarePaginator::class, $pagination);

        $this->assertEquals($count, $pagination->count());
    }

    /**
     * Test create model
     */
    public function test_create(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $program = $repository->create($user->getKey(), [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ]);

        $this->assertInstanceOf(Program::class, $program);
    }

    /**
     * Test create model without title
     */
    public function test_create_without_title(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test create model without description
     */
    public function test_create_without_description(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'title' => fake()->title(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test create model without start_date
     */
    public function test_create_without_start_date(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test create model without end_date
     */
    public function test_create_without_end_date(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $foundProgram = $repository->read($program->getKey());

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
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $repository->read($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $updatedProgram = $repository->update($program->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Program::class, $updatedProgram);

        $this->assertEquals($newAttributes, [
            'title' => $updatedProgram->title,
            'description' => $updatedProgram->description,
            'start_date' => $updatedProgram->start_date,
            'end_date' => $updatedProgram->end_date,
        ]);

        $this->assertEquals($newUser->getKey(), $updatedProgram->user_id);
    }

    /**
     * Test update model without title
     */
    public function test_update_without_title(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $updatedProgram = $repository->update($program->getKey(), $newAttributes, $newUser->getKey());

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
            ]);

        $this->assertEquals($newUser->getKey(), $updatedProgram->user_id);
    }

    /**
     * Test update model without description
     */
    public function test_update_without_description(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->title(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $updatedProgram = $repository->update($program->getKey(), $newAttributes, $newUser->getKey());

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
            ]);

        $this->assertEquals($newUser->getKey(), $updatedProgram->user_id);
    }

    /**
     * Test update model without start_date
     */
    public function test_update_without_start_date(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $updatedProgram = $repository->update($program->getKey(), $newAttributes, $newUser->getKey());

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
            ]);

        $this->assertEquals($newUser->getKey(), $updatedProgram->user_id);
    }

    /**
     * Test update model without end_date
     */
    public function test_update_without_end_date(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $updatedProgram = $repository->update($program->getKey(), $newAttributes, $newUser->getKey());

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
            ]);

        $this->assertEquals($newUser->getKey(), $updatedProgram->user_id);
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $newAttributes = [
            'title' => fake()->title(),
            'description' => fake()->realText(),
            'start_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
            'end_date' => fake()->dateTime()->format('Y-m-d H:i:s'),
        ];

        $repository->update($this->faker->numberBetween(), $newAttributes);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $program = Program::factory()->create();

        $this->assertInstanceOf(Program::class, $program);

        $deleteResult = $repository->delete($program->getKey());

        $this->assertTrue($deleteResult);
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var ProgramRepository $repository
         */
        $repository = app(ProgramRepository::class);

        $repository->delete($this->faker->randomNumber());
    }
}
