<?php

namespace App\Repositories;

use App\Models\User;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class UserRepository {
     /**
     * @var User $user
     */
    private $user;

    /**
     * UserRepository constructor.
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }
    /**
     * Paginated list
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return $this->user->paginate(10);
    }

    /**
     * Create model
     *
     * @param array $attributes
     * @param array|null $programs
     * @return User
     */
    public function create(array $attributes, array $programs = null): User
    {
        try {
            return DB::transaction(function() use ($attributes, $programs) {
                /**
                 * @var User $user
                 */
                $user = $this->user->create($attributes);
                if($programs && count($programs) > 0){
                    $user->participants()->syncWithoutDetaching($programs);
                }
                $user->save();
                return $user->refresh();
            });
        } catch (Exception $e) {
            \Log::error($e->getMessage(), array('e' => $e));
            return throw $e;
        }
    }

    /**
     * Find model
     * 
     * @param string $id
     * @return User
     */
    public function read(string $id): User
    {
        return $this->user->findOrFail($id);
    }

    /**
     * Update model
     *
     * @param  string  $id
     * @param  array  $attributes
     * @return User
     */
    public function update(string $id, array $attributes, array $programs = null): User
    {
        try {
            return DB::transaction(function() use ($id, $attributes, $programs) {
                /**
                 * @var User $user
                 */
                $user = $this->user->findOrFail($id);
                if($programs && count($programs) > 0){
                    $user->participants()->syncWithoutDetaching($programs);
                }
                $user->update($attributes);
                return $user->refresh();
            });
        } catch (Exception $e) {
            \Log::error($e->getMessage(), array('e' => $e));
            return throw $e;
        }
    }

    /**
     * Delete model
     * 
     * @param string $id
     * @return User
     */
    public function delete(string $id): bool|null
    {
        $user = $this->user->findOrFail($id);
        return $user->delete();
    }
}