<?php

namespace App\Services;

use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;

class CategoryService
{
    public function __construct(protected CategoryRepositoryInterface $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function getBySlug(string $slug): Category
    {
        return $this->repository->getBySlug($slug);
    }

    public function countRelatedTickets(): array
    {
        return $this->repository->countRelatedTickets();
    }

    public function create(array $data): Category
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Category
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id): Category
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
