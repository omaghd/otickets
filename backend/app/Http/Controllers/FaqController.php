<?php

namespace App\Http\Controllers;

use App\Http\Requests\Faqs\StoreFaqRequest;
use App\Http\Requests\Faqs\UpdateFaqRequest;
use App\Services\FaqService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class FaqController extends Controller
{
    public function __construct(protected FaqService $service)
    {
    }

    public function index()
    {
        $faqs = $this->service->getAll();

        return $this->successResponse(data: $faqs);
    }

    public function getSuggestions(string $query): JsonResponse
    {
        $faqs = $this->service->getSuggestions($query);

        return $this->successResponse(data: $faqs);
    }

    public function show($slug): JsonResponse
    {
        try {
            $faq = $this->service->getBySlug($slug);

            return $this->successResponse(data: $faq);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function store(StoreFaqRequest $request): JsonResponse
    {
        $faq = $this->service->create($request->validated());

        return $this->successResponse('Faq created successfully', $faq);
    }

    public function update(UpdateFaqRequest $request, $id): JsonResponse
    {
        try {
            $faq = $this->service->update($id, $request->validated());

            return $this->successResponse('Faq updated successfully', $faq);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy($id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('Faq deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function forceDelete($id): JsonResponse
    {
        try {
            $this->service->forceDelete($id);

            return $this->successResponse('Faq force deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function restore($id): JsonResponse
    {
        try {
            $this->service->restore($id);

            return $this->successResponse('Faq restored successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
