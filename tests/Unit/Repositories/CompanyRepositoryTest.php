<?php

namespace Tests\Unit\Repositories;
use App\Models\Company;
use App\Models\User;
use App\Repositories\CompanyRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompanyRepositoryTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    /**
     * Test empty pagination
     */
    public function test_empty_pagination(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

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

        $companies = Company::factory()->count($count)->create();

        $this->assertCount($count, $companies);
        
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

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
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $company = $repository->create($user->getKey(), [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ]);

        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * Test create model without name
     */
    public function test_create_without_name(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ]);
    }

    /**
     * Test create model without location
     */
    public function test_create_without_location(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'industry' => fake()->company(),
        ]);
    }

    /**
     * Test create model without industry
     */
    public function test_create_without_industry(): void
    {
        $this->expectException(QueryException::class);

        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $repository->create($user->getKey(), [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
        ]);
    }

    /**
     * Test create model without image_path
     */
    public function test_create_without_image_path(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $user = User::factory()->create();

        $this->assertInstanceOf(User::class, $user);

        $company = $repository->create($user->getKey(), [
            'name' => fake()->name,
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ]);

        $this->assertInstanceOf(Company::class, $company);
    }

    /**
     * Test find model
     */
    public function test_read(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $foundCompany = $repository->read($company->getKey());

        $this->assertInstanceOf(Company::class, $foundCompany);

        $this->assertTrue($company->is($foundCompany));
    }

    /**
     * Test find missing model
     */
    public function test_read_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $repository->read($this->faker->randomNumber());
    }

    /**
     * Test update model
     */
    public function test_update(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $updatedCompany = $repository->update($company->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals($newAttributes, [
            'name' => $updatedCompany->name,
            'image_path' => $updatedCompany->image_path,
            'location' => $updatedCompany->location,
            'industry' => $updatedCompany->industry,
        ]);

        $this->assertEquals($newUser->getKey(), $updatedCompany->user_id);
    }

    /**
     * Test update model without name
     */
    public function test_update_without_name(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $updatedCompany = $repository->update($company->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'name' => $company->name,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedCompany->user_id);
    }

    /**
     * Test update model without image_path
     */
    public function test_update_without_image_path(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $updatedCompany = $repository->update($company->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'image_path' => $company->image_path,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedCompany->user_id);
    }

    /**
     * Test update model without location
     */
    public function test_update_without_location(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'industry' => fake()->company(),
        ];

        $updatedCompany = $repository->update($company->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'location' => $company->location,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedCompany->user_id);
    }

    /**
     * Test update model without industry
     */
    public function test_update_without_industry(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $newUser = User::factory()->create();

        $this->assertInstanceOf(User::class, $newUser);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
        ];

        $updatedCompany = $repository->update($company->getKey(), $newAttributes, $newUser->getKey());

        $this->assertInstanceOf(Company::class, $updatedCompany);

        $this->assertEquals(
            array_merge([
                'industry' => $company->industry,
            ], $newAttributes),
            [
                'name' => $updatedCompany->name,
                'image_path' => $updatedCompany->image_path,
                'location' => $updatedCompany->location,
                'industry' => $updatedCompany->industry,
            ]);

        $this->assertEquals($newUser->getKey(), $updatedCompany->user_id);
    }

    /**
     * Test update missing model
     */
    public function test_update_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $newAttributes = [
            'name' => fake()->name,
            'image_path' => fake()->imageUrl(),
            'location' => fake()->country(),
            'industry' => fake()->company(),
        ];

        $repository->update($this->faker->numberBetween(), $newAttributes);
    }

    /**
     * Test delete model
     */
    public function test_delete(): void
    {
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $company = Company::factory()->create();

        $this->assertInstanceOf(Company::class, $company);

        $deleteResult = $repository->delete($company->getKey());

        $this->assertTrue($deleteResult);
    }

    /**
     * Test delete missing model
     */
    public function test_delete_not_found(): void
    {
        $this->expectException(ModelNotFoundException::class);
        /**
         * @var CompanyRepository $repository
         */
        $repository = app(CompanyRepository::class);

        $repository->delete($this->faker->randomNumber());
    }
}
