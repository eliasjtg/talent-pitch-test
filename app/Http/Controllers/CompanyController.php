<?php

namespace App\Http\Controllers;

use App\Repositories\CompanyRepository;
use App\Models\Company;
use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class CompanyController extends Controller
{
    /**
     * @var CompanyRepository $companyRepository
     */
    private CompanyRepository $companyRepository;

    /**
     * CompanyController constructor.
     * @param CompanyRepository $companyRepository
     */
    public function __construct(CompanyRepository $companyRepository)
    {
        $this->companyRepository = $companyRepository;
    }
    /**
     * Paginate list
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return $this->companyRepository->paginate();
    }

    /**
     * Create model
     *
     * @param StoreCompanyRequest $request
     * @return Company
     */
    public function store(StoreCompanyRequest $request): Company
    {
        return $this->companyRepository->create($request->get('user_id'), $request->except(['user_id', 'programs']), $request->get('programs'));
    }

    /**
     * Find model
     *
     * @param string $id
     * @return Company
     */
    public function show(string $id): Company
    {
        return $this->companyRepository->read($id);
    }

    /**
     * Update model
     *
     * @param StoreCompanyRequest $request
     * @return Company
     */
    public function update(UpdateCompanyRequest $request, string $id): Company
    {
        return $this->companyRepository->update($id, $request->except(['user_id', 'programs']), $request->get('user_id'), $request->get('programs'));
    }

    /**
     * Delete model
     *
     * @param string $id
     * @return void
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => (bool) $this->companyRepository->delete($id),
        ]);
    }
}