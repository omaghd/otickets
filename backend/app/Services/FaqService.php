<?php

namespace App\Services;

use App\Interfaces\FaqRepositoryInterface;
use App\Models\Faq;

class FaqService
{
    public function __construct(protected FaqRepositoryInterface $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getBySlug(string $slug): Faq
    {
        return $this->repository->getBySlug($slug);
    }

    public function getSuggestions(string $query): array
    {
        return $this->repository->getSuggestions($query);
    }

    public function create(array $data): Faq
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Faq
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id): Faq
    {
        return $this->repository->find($id);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }

    public function restore(int $id): void
    {
        $this->repository->restore($id);
    }

    public function forceDelete(int $id): void
    {
        $this->repository->forceDelete($id);
    }
}
