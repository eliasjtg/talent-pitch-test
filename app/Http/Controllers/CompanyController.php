<?php

namespace App\Http\Controllers;

use App\Http\Requests\Programs\ProgramsRequest;
use App\Jobs\GPTSeeder\CompaniesFill;
use App\Repositories\CompanyRepository;
use App\Models\Company;
use App\Http\Requests\Companies\StoreCompanyRequest;
use App\Http\Requests\Companies\UpdateCompanyRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

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
     * @param ProgramsRequest $program
     * @param string $userId
     * @return Company
     */
    public function store(StoreCompanyRequest $request, ProgramsRequest $program, string $userId): Company
    {
        return $this->companyRepository->create($userId, $request->all(), $program->get('programs'));
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
     * @param ProgramsRequest $program
     * @param string $id
     * @return Company
     */
    public function update(UpdateCompanyRequest $request, ProgramsRequest $program, string $id): Company
    {
        return $this->companyRepository->update($id, $request->except(['user_id']), $request->get('user_id'), $program->get('programs'));
    }

    /**
     * Delete model
     *
     * @param string $id
     * @return JsonResponse
     */
    public function destroy(string $id): JsonResponse
    {
        return response()->json([
            'success' => (bool) $this->companyRepository->delete($id),
        ]);
    }

    /**
     * Fill using gpt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function gpt()
    {
        CompaniesFill::dispatch();

        return response()->json([
            'success' => true,
            'message' => 'Fill with GPT process will be run quickly'
        ]);
    }
}