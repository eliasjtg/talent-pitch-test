<?php

namespace App\Http\Controllers;

use App\Http\Requests\Programs\ProgramsRequest;
use App\Jobs\GPTSeeder\UsersFill;
use App\Repositories\UserRepository;
use App\Models\User;
use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserController extends Controller
{
    /**
     * @var UserRepository $userRepository
     */
    private UserRepository $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }
    /**
     * Paginate list
     *
     * @return LengthAwarePaginator
     */
    public function index(): LengthAwarePaginator
    {
        return $this->userRepository->paginate();
    }

    /**
     * Create model
     *
     * @param StoreUserRequest $request
     * @return User
     */
    public function store(StoreUserRequest $request, ProgramsRequest $program): User
    {
        return $this->userRepository->create($request->all(), $program->get('programs'));
    }

    /**
     * Find model
     *
     * @param string $id
     * @return User
     */
    public function show(string $id): User
    {
        return $this->userRepository->read($id);
    }

    /**
     * Update model
     *
     * @param StoreUserRequest $request
     * @param ProgramsRequest $program
     * @return User
     */
    public function update(UpdateUserRequest $request, ProgramsRequest $program, string $id): User
    {
        return $this->userRepository->update($id, $request->all(), $program->get('programs'));
    }

    /**
     * Delete model
     *
     * @param string $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(string $id)
    {
        return response()->json([
            'success' => (bool) $this->userRepository->delete($id),
        ]);
    }

    /**
     * Fill using gpt
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function gpt()
    {
        UsersFill::dispatch();

        return response()->json([
            'success' => true,
            'message' => 'Fill with GPT process will be run quickly'
        ]);
    }
}