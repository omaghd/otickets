<?php

namespace App\Http\Controllers;

use App\Http\Requests\Clients\StoreClientRequest;
use App\Http\Requests\Clients\UpdateClientRequest;
use App\Services\UserService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class ClientController extends Controller
{
    public function __construct(protected UserService $service)
    {
    }

    public function index(): JsonResponse
    {
        $clients = $this->service->getClients();

        return $this->successResponse(data: $clients);
    }

    public function store(StoreClientRequest $request): JsonResponse
    {
        $client = $this->service->create($request->validated(), isClient: true);

        if ($request->hasFile('picture'))
            $this->service->saveProfilePicture($client, $request->file('picture'));

        return $this->successResponse('Client created successfully', $client);
    }

    public function update(UpdateClientRequest $request, $id): JsonResponse
    {
        try {
            $client = $this->service->update($id, $request->validated());

            if ($request->hasFile('picture'))
                $this->service->saveProfilePicture($client, $request->file('picture'));

            return $this->successResponse('Client updated successfully', $client);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('Client deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $this->service->forceDelete($id);

            return $this->successResponse('Client force deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $this->service->restore($id);

            return $this->successResponse('Client restored successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
