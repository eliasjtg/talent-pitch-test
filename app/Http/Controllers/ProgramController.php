<?php

namespace App\Http\Controllers;

use App\Jobs\GPTSeeder\ProgramsFill;
use App\Repositories\ProgramRepository;
use App\Models\Program;
use App\Http\Requests\Programs\StoreProgramRequest;
use App\Http\Requests\Programs\UpdateProgramRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ProgramController extends Controller
{
    /**
     * @var ProgramRepository $programRepository
     */
    private ProgramRepository $programRepository;

    /**
     * ProgramController constructor.
     * @param ProgramRepository $programRepository
     */
    public function __construct(ProgramRepository $programRepository)
    {
        $this->programRepository = $programRepository;
    }
    /**
     * Paginate list
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return $this->programRepository->paginate();
    }

    /**
     * Create model
     *
     * @param StoreProgramRequest $request
     * @return Program
     */
    public function store(StoreProgramRequest $request, string $userId): Program
    {
        return $this->programRepository->create($userId, $request->all());
    }

    /**
     * Find model
     *
     * @param string $id
     * @return Program
     */
    public function show(string $id): Program
    {
        return $this->programRepository->read($id);
    }

    /**
     * Update model
     *
     * @param StoreProgramRequest $request
     * @return Program
     */
    public function update(UpdateProgramRequest $request, string $id): Program
    {
        return $this->programRepository->update($id, $request->except(['user_id']), $request->get('user_id'));
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
            'success' => (bool) $this->programRepository->delete($id),
        ]);
    }

    /**
     * Fill using gpt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function gpt()
    {
        ProgramsFill::dispatch();

        return response()->json([
            'success' => true,
            'message' => 'Fill with GPT process will be run quickly'
        ]);
    }
}