<?php

namespace App\Repositories;

use App\Models\Challenge;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\DB;

class ChallengeRepository {
     /**
     * @var Challenge $challenge
     */
    private $challenge;

    /**
    * @var User $user
    */
   private $user;

    /**
     * ChallengeRepository constructor.
     * @param Challenge $challenge
     * @param User $user
     */
    public function __construct(Challenge $challenge, User $user)
    {
        $this->challenge = $challenge;
        $this->user = $user;
    }
    /**
     * Paginated list
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return $this->challenge->newQuery()->with(['user'])->paginate(10);
    }

    /**
     * Create model
     *
     * @param User|int $user
     * @param array $attributes
     * @param array|null $programs
     * @return Challenge
     */
    public function create(User|int $user, array $attributes, array $programs = null): Challenge
    {
        try {
            return DB::transaction(function() use ($user, $attributes, $programs) {
                /**
                 * @var Challenge $challenge
                 */
                $challenge = new Challenge($attributes);
                $challenge->user()->associate($user);
                $challenge->save();
                if($programs && count($programs) > 0){
                    $challenge->participants()->syncWithoutDetaching($programs);
                }
                return $challenge->refresh()->load(['user', 'participants']);
            });
        } catch (Exception $e) {
            \Log::error($e->getMessage(), array('e' => $e));
            return throw $e;
        }
        
    }

    /**
     * Find model
     * 
     * @param $id
     * @return Challenge
     */
    public function read(string $id): Challenge
    {
        return $this->challenge->newQuery()->with(['user', 'participants'])->findOrFail($id);
    }

    /**
     * Update model
     *
     * @param  string  $id
     * @param  array  $attributes
     * @param  int|null  $user
     * @param  array|null  $programs
     * @return Challenge
     */
    public function update(string $id, array $attributes, int $user = null, array $programs = null): Challenge
    {

        try {
            return DB::transaction(function() use ($id, $attributes, $user, $programs) {
                /**
                 * @var Challenge $challenge
                 */
                
                $challenge = $this->challenge->findOrFail($id);
                if($user){
                    $challenge->user()->associate($user);
                }
                $challenge->update($attributes);
                if($programs && count($programs) > 0){
                    $challenge->participants()->sync($programs);
                }
                return $challenge->refresh()->load(['user', 'participants']);
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
     * @return Challenge
     */
    public function delete(string $id): bool|null
    {
        $challenge = $this->challenge->findOrFail($id);
        return $challenge->delete();
    }
}