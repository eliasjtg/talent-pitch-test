<?php

namespace App\Http\Controllers;

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
     * @return Challenge
     */
    public function store(StoreChallengeRequest $request): Challenge
    {
        return $this->challengeRepository->create($request->get('user_id'), $request->except(['user_id', 'programs']), $request->get('programs'));
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
     * @return Challenge
     */
    public function update(UpdateChallengeRequest $request, string $id): Challenge
    {
        return $this->challengeRepository->update($id, $request->except(['user_id', 'programs']), $request->get('user_id'), $request->get('programs'));
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
}