<?php

namespace App\Http\Controllers;

use App\Http\Requests\Programs\ProgramsRequest;
use App\Jobs\GPTSeeder\ChallengesFill;
use App\Repositories\ChallengeRepository;
use App\Models\Challenge;
use App\Http\Requests\Challenges\StoreChallengeRequest;
use App\Http\Requests\Challenges\UpdateChallengeRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\JsonResponse;

class ChallengeController extends Controller
{
    /**
     * @var ChallengeRepository $challengeRepository
     */
    private ChallengeRepository $challengeRepository;

    /**
     * ChallengeController constructor.
     * @param ChallengeRepository $challengeRepository
     */
    public function __construct(ChallengeRepository $challengeRepository)
    {
        $this->challengeRepository = $challengeRepository;
    }
    /**
     * Paginate list
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return $this->challengeRepository->paginate();
    }

    /**
     * Create model
     *
     * @param StoreChallengeRequest $request
     * @param ProgramsRequest $program
     * @param string $userId
     * @return Challenge
     */
    public function store(StoreChallengeRequest $request, ProgramsRequest $program, string $userId): Challenge
    {
        return $this->challengeRepository->create($userId, $request->all(), $program->get('programs'));
    }

    /**
     * Find model
     *
     * @param string $id
     * @return Challenge
     */
    public function show(string $id): Challenge
    {
        return $this->challengeRepository->read($id);
    }

    /**
     * Update model
     *
     * @param StoreChallengeRequest $request
     * @param ProgramsRequest $program
     * @param string $id
     * @return Challenge
     */
    public function update(UpdateChallengeRequest $request, ProgramsRequest $program, string $id): Challenge
    {
        return $this->challengeRepository->update($id, $request->except('user_id'), $request->get('user_id'), $program->get('programs'));
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
            'success' => (bool) $this->challengeRepository->delete($id),
        ]);
    }

    /**
     * Fill using gpt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function gpt()
    {
        ChallengesFill::dispatch();

        return response()->json([
            'success' => true,
            'message' => 'Fill with GPT process will be run quickly'
        ]);
    }
}