<?php

namespace Tests\Unit\Repositories;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

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

        $users = User::factory()->count($count)->create();

        $this->assertCount($count, $users);
        
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

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
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = $repository->create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $repository->create([
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ]);
    }

    /**
     * Test create model without email
     */
    public function test_create_without_email(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $repository->create([
            'name' => $this->faker->name,
            'image_path' => $this->faker->imageUrl(),
        ]);
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = $repository->create([
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ]);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $foundUser = $repository->read($user->getKey());

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
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $repository->read($this->faker->numberBetween());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $updatedUser = $repository->update($user->getKey(), $newAttributes);

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
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $updatedUser = $repository->update($user->getKey(), $newAttributes);

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
        array_merge([
            'name' => $user->name,
        ], $newAttributes),
        [
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'image_path' => $updatedUser->image_path,
        ]);
    }

    /**
     * Test update model without email
     */
    public function test_update_without_email(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'image_path' => $this->faker->imageUrl(),
        ];

        $updatedUser = $repository->update($user->getKey(), $newAttributes);

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
        array_merge([
            'email' => $user->email,
        ], $newAttributes),
        [
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'image_path' => $updatedUser->image_path,
        ]);
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
        ];

        $updatedUser = $repository->update($user->getKey(), $newAttributes);

        $this->assertInstanceOf(User::class, $updatedUser);

        $this->assertEquals(
        array_merge([
            'image_path' => $user->image_path,
        ], $newAttributes),
        [
            'name' => $updatedUser->name,
            'email' => $updatedUser->email,
            'image_path' => $updatedUser->image_path,
        ]);
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $newAttributes = [
            'name' => $this->faker->name,
            'email' => $this->faker->email,
            'image_path' => $this->faker->imageUrl(),
        ];

        $repository->update($this->faker->numberBetween(), $newAttributes);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $deleteResult = $repository->delete($user->getKey());

        $this->assertTrue($deleteResult);
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var UserRepository $repository
         */
        $repository = app(UserRepository::class);

        $repository->delete($this->faker->numberBetween());
    }
}
