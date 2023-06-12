<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsLetter\StoreNewsLetterRequest;
use App\Http\Requests\NewsLetter\UpdateNewsLetterRequest;
use App\Services\NewsletterService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpFoundation\JsonResponse;

class NewsletterController extends Controller
{
    public function __construct(protected NewsletterService $service)
    {
    }

    public function index(): JsonResponse
    {
        $newsLetters = $this->service->getAll();

        return $this->successResponse(data: $newsLetters);
    }

    public function store(StoreNewsLetterRequest $request): JsonResponse
    {
        $newsLetter = $this->service->create($request->validated());

        return $this->successResponse('Email added successfully', $newsLetter);
    }

    public function update(UpdateNewsLetterRequest $request, int $id): JsonResponse
    {
        try {
            $newsLetter = $this->service->update($id, $request->validated());

            return $this->successResponse('Email updated successfully', $newsLetter);
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->service->delete($id);

            return $this->successResponse('Email deleted successfully');
        } catch (ModelNotFoundException $e) {
            return $this->errorResponse($e->getMessage(), 404);
        }
    }
}
