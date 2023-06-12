<?php

namespace App\Repositories;

use App\Interfaces\DepartmentRepositoryInterface;
use App\Models\Department;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class DepartmentRepository implements DepartmentRepositoryInterface
{
    public function getAll(): array
    {
        return Department::query()
            ->getTrashed()
            ->search()
            ->withAgents()
            ->withCategories()
            ->withAgentsCount()
            ->withCategoriesCount()
            ->latest()
            ->paginateOrGet();
    }

    public function create(array $data): Department
    {
        return Department::create($data);
    }

    public function update(int $id, array $data): Department
    {
        $department = $this->find($id);
        $department->update($data);

        return $department;
    }

    public function find(int $id): Department
    {
        try {
            return Department::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Department not found');
        }
    }

    public function delete(int $id): void
    {
        $department = $this->find($id);
        $department->delete();
    }

    public function restore(int $id): void
    {
        $department = $this->findInTrash($id);
        $department->restore();
    }

    public function findInTrash(int $id): Department
    {
        try {
            return Department::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Department not found');
        }
    }

    public function forceDelete(int $id): void
    {
        $department = $this->findInTrash($id);
        $department->forceDelete();
    }
}
