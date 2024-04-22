<?php

namespace App\Repositories;

use App\Models\Company;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Exception;
use Illuminate\Support\Facades\DB;

class CompanyRepository {
     /**
     * @var Company $company
     */
    private $company;
    
     /**
     * @var User $user
     */
    private $user;

    /**
     * CompanyRepository constructor.
     * @param Company $company
     * @param User $user
     */
    public function __construct(Company $company, User $user)
    {
        $this->company = $company;
        $this->user = $user;
    }
    /**
     * Paginated list
     *
     * @return LengthAwarePaginator
     */
    public function paginate(): LengthAwarePaginator
    {
        return $this->company->newQuery()->with(['user'])->paginate(10);
    }

    /**
     * Create model
     *
     * @param int $user
     * @param array $attributes
     * @param array|null $programs
     * @return Company
     */
    public function create(int $user, array $attributes, array $programs = null): Company
    {

        try {
            return DB::transaction(function() use ($user, $attributes, $programs) {
                
                /**
                 * @var Company $company
                 */

                 $company = new Company($attributes);
                 $user = $user instanceof User ? $user : $this->user->findOrFail($user);
                 $company->user()->associate($user);
                 $company->save();
                 if($programs && count($programs) > 0){
                     $company->participants()->syncWithoutDetaching($programs);
                 }
                 return $company->refresh();
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
     * @return Company
     */
    public function read(string $id): Company
    {
        return $this->company->newQuery()->with(['user'])->findOrFail($id);
    }

    /**
     * Update model
     *
     * @param  string  $id
     * @param  array  $attributes
     * @param  int|null  $user
     * @param  array|null  $programs
     * @return Company
     */
    public function update(string $id, array $attributes, int $user = null, array $programs = null): Company
    {

        try {
            return DB::transaction(function() use ($id, $attributes, $user, $programs) {
                
                /**
                 * @var Company $company
                 */
                $company = $this->company->findOrFail($id);
                if($user){
                    $company->user()->associate($user);
                }
                if($programs && count($programs) > 0){
                    $company->participants()->syncWithoutDetaching($programs);
                }
                $company->update($attributes);
                return $company->refresh();
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
     * @return Company
     */
    public function delete(string $id): bool|null
    {
        $company = $this->company->findOrFail($id);
        return $company->delete();
    }
}