<?php

namespace App\Http\Controllers;

use App\Http\Requests\CannedResponses\StoreCannedResponseRequest;
use App\Http\Requests\CannedResponses\UpdateCannedResponseRequest;
use App\Models\CannedResponse;
use App\Services\CannedResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CannedResponseController extends Controller
{
    public function __construct(protected CannedResponseService $service)
    {
    }

    public function index()
    {
        try {
            $this->authorize('viewAny', CannedResponse::class);

            $cannedResponses = $this->service->getAll();

            return $this->successResponse(data: $cannedResponses);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function store(StoreCannedResponseRequest $request): JsonResponse
    {
        $cannedResponse = $this->service->create($request->validated());

        return $this->successResponse('Canned Response created successfully', $cannedResponse);
    }

    public function update(UpdateCannedResponseRequest $request, $id): JsonResponse
    {
        try {
            $cannedResponse = $this->service->find($id);

            $this->authorize('update', $cannedResponse);

            $cannedResponse = $this->service->update($id, $request->validated());

            return $this->successResponse('Canned Response updated successfully', $cannedResponse);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This Canned Response does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $cannedResponse = $this->service->find($id);

            $this->authorize('delete', $cannedResponse);

            $this->service->delete($id);

            return $this->successResponse('Canned Response deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This Canned Response does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $cannedResponse = $this->service->findInTrash($id);

            $this->authorize('forceDelete', $cannedResponse);

            $this->service->forceDelete($id);

            return $this->successResponse('Canned Response force deleted successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This Canned Response does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $cannedResponse = $this->service->findInTrash($id);

            $this->authorize('restore', $cannedResponse);

            $this->service->restore($id);

            return $this->successResponse('Canned Response restored successfully');
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This Canned Response does not exist', 404);
        } catch (AuthorizationException) {
            return $this->notAuthorizedResponse();
        }
    }
}
