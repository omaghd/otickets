<?php

namespace App\Http\Controllers;

use App\Http\Requests\Categories\StoreCategoryRequest;
use App\Http\Requests\Categories\UpdateCategoryRequest;
use App\Services\CategoryService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    public function __construct(protected CategoryService $service)
    {
    }

    public function index()
    {
        $categories = $this->service->getAll();

        return $this->successResponse(data: $categories);
    }

    public function countRelatedTickets(): JsonResponse
    {
        return $this->successResponse(data: $this->service->countRelatedTickets());
    }

    public function show($slug): JsonResponse
    {
        try {
            $category = $this->service->getBySlug($slug);

            return $this->successResponse(data: $category);
        } catch (ModelNotFoundException) {
            return $this->errorResponse('This category does not exist', 404);
        }
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->service->create($request->validated());

        return $this->successResponse('Category created successfully', $category);
    }

    public function update(UpdateCategoryRequest $request, $id): JsonResponse
    {
        try {
            $category = $this->service->update($id, $request->validated());

            return $this->successResponse('Category updated successfully', $category);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('Category deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $this->service->forceDelete($id);

            return $this->successResponse('Category force deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $this->service->restore($id);

            return $this->successResponse('Category restored successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
