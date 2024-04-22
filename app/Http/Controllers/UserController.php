<?php

namespace App\Http\Controllers;

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
    public function store(StoreUserRequest $request): User
    {
        return $this->userRepository->create($request->except(['programs']), $request->get('programs'));
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
     * @return User
     */
    public function update(UpdateUserRequest $request, string $id): User
    {
        return $this->userRepository->update($id, $request->except(['programs']), $request->get('programs'));
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
}