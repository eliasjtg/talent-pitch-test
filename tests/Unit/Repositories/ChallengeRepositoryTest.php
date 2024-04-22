<?php

namespace Tests\Unit\Repositories;
use App\Models\Challenge;
use App\Models\User;
use App\Repositories\ChallengeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ChallengeRepositoryTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

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

        $challenges = Challenge::factory()->count($count)->create();

        $this->assertCount($count, $challenges);
        
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

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
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $challenge = $repository->create($user->getKey(), [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ]);

        $this->assertInstanceOf(Challenge::class, $challenge);
    }

    /**
     * Test create model without title
     */
    public function test_create_without_title(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Test create model without description
     */
    public function test_create_without_description(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'title' => fake()->text(20),
            'difficulty' => fake()->numberBetween(1, 10),
        ]);
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_dificulty(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
        ]);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $foundChallenge = $repository->read($challenge->getKey());

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
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $repository->read($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $updatedChallenge = $repository->update($challenge->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals($newAttributes, [
            'title' => $updatedChallenge->title,
            'description' => $updatedChallenge->description,
            'difficulty' => $updatedChallenge->difficulty,
        ]);

        $this->assertEquals($newUser->getKey(), $updatedChallenge->user_id);
    }

    /**
     * Test update model without title
     */
    public function test_update_without_title(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $updatedChallenge = $repository->update($challenge->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'title' => $challenge->title,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedChallenge->user_id);
    }

    /**
     * Test update model without description
     */
    public function test_update_without_description(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->text(20),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $updatedChallenge = $repository->update($challenge->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'description' => $challenge->description,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedChallenge->user_id);
    }

    /**
     * Test update model without difficulty
     */
    public function test_update_without_difficulty(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
        ];

        $updatedChallenge = $repository->update($challenge->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Challenge::class, $updatedChallenge);

        $this->assertEquals(
            array_merge([
                'difficulty' => $challenge->difficulty,
            ], $newAttributes),
            [
                'title' => $updatedChallenge->title,
                'description' => $updatedChallenge->description,
                'difficulty' => $updatedChallenge->difficulty,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedChallenge->user_id);
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $newAttributes = [
            'title' => fake()->text(20),
            'description' => fake()->realText(),
            'difficulty' => fake()->numberBetween(1, 10),
        ];

        $repository->update($this->faker->numberBetween(), $newAttributes);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $challenge = Challenge::factory()->create();

        $this->assertInstanceOf(Challenge::class, $challenge);

        $deleteResult = $repository->delete($challenge->getKey());

        $this->assertTrue($deleteResult);
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var ChallengeRepository $repository
         */
        $repository = app(ChallengeRepository::class);

        $repository->delete($this->faker->randomNumber());
    }
}
