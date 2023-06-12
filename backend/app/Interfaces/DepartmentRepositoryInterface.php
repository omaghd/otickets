<?php

namespace App\Interfaces;

use App\Models\Department;

interface DepartmentRepositoryInterface
{
    public function getAll(): array;

    public function find(int $id): Department;

    public function findInTrash(int $id): Department;

    public function create(array $data): Department;

    public function update(int $id, array $data): Department;

    public function delete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
