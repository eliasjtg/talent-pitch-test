<?php

namespace App\Repositories;

use App\Models\Program;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\DB;

class ProgramRepository {
     /**
     * @var Program $program
     */
    private $program;
     /**
     * @var User $user
     */
    private $user;

    /**
     * ProgramRepository constructor.
     * @param Program $program
     * @param User $user
     */
    public function __construct(Program $program, User $user)
    {
        $this->program = $program;
        $this->user = $user;
    }
    /**
     * Paginated list
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return $this->program->newQuery()->with(['user'])->paginate(10);
    }

    /**
     * Create model
     *
     * @param int $user
     * @param array $attributes
     * @return Program
     */
    public function create(User|int $user, array $attributes): Program
    {

        try {
            return DB::transaction(function() use ($user, $attributes) {
                /**
                 * @var Program $program
                 */
                $program = new Program($attributes);
                $program->user()->associate($user);
                $program->save();
                return $program->load('user');
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
     * @return Program
     */
    public function read(string $id): Program
    {
        return $this->program->newQuery()->with(['user'])->findOrFail($id);
    }

    /**
     * Update model
     *
     * @param  string  $id
     * @param  array  $attributes
     * @param  int  $user
     * @return Program
     */
    public function update(string $id, array $attributes, int $user = null): Program
    {

        try {
            return DB::transaction(function() use ($id, $attributes, $user) {
                /**
                 * @var Program $program
                 */
                
                $program = $this->program->findOrFail($id);
                if($user){
                    $program->user()->associate($user);
                }
                $program->update($attributes);
                return $program->refresh()->load('user');
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
     * @return Program
     */
    public function delete(string $id): bool|null
    {
        $program = $this->program->findOrFail($id);
        return $program->delete();
    }
}