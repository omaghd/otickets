<?php

namespace App\Http\Controllers;

use App\Http\Requests\Departments\StoreDepartmentRequest;
use App\Http\Requests\Departments\UpdateDepartmentRequest;
use App\Services\DepartmentService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class DepartmentController extends Controller
{
    public function __construct(protected DepartmentService $service)
    {
    }

    public function index(): JsonResponse
    {
        $departments = $this->service->getAll();

        return $this->successResponse(data: $departments);
    }

    public function store(StoreDepartmentRequest $request): JsonResponse
    {
        $department = $this->service->create($request->validated());

        return $this->successResponse('Department created successfully', $department);
    }

    public function update(UpdateDepartmentRequest $request, $id): JsonResponse
    {
        try {
            $department = $this->service->update($id, $request->validated());

            return $this->successResponse('Department updated successfully', $department);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('Department deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $this->service->forceDelete($id);

            return $this->successResponse('Department force deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $this->service->restore($id);

            return $this->successResponse('Department restored successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
