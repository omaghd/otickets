<?php

namespace App\Services;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;

class DepartmentService
{
    public function __construct(protected DepartmentRepositoryInterface $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function create(array $data): Department
    {
        return $this->repository->create($data);
    }

    public function update(int $id, array $data): Department
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id): Department
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
