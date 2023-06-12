<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function index(): JsonResponse
    {
        $users = $this->service->getAdminsAndAgents();

        return $this->successResponse(data: $users);
    }

    public function countRelatedTicketsByAgentAndPriority(): JsonResponse
    {
        return $this->successResponse(data: $this->service->countRelatedTicketsByAgentAndPriority());
    }

    public function getClientsStats(): JsonResponse
    {
        return $this->successResponse(data: $this->service->getClientsStats());
    }

    public function getAgentsResponseTime()
    {
        return $this->successResponse(data: $this->service->getAgentsResponseTime());
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $user = $this->service->create($request->validated());

        if ($request->hasFile('picture'))
            $this->service->saveProfilePicture($user, $request->file('picture'));

        return $this->successResponse('User created successfully', $user);
    }

    public function update(UpdateUserRequest $request, $id): JsonResponse
    {
        try {
            $user = $this->service->update($id, $request->validated());

            if ($request->hasFile('picture'))
                $this->service->saveProfilePicture($user, $request->file('picture'));

            return $this->successResponse('User updated successfully', $user);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('User deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $this->service->forceDelete($id);

            return $this->successResponse('User force deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $this->service->restore($id);

            return $this->successResponse('User restored successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
