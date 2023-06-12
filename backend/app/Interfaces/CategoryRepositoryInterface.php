<?php

namespace App\Interfaces;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function getAll(): array;

    public function getBySlug(string $slug): Category;

    public function countRelatedTickets(): array;

    public function find(int $id): Category;

    public function findInTrash(int $id): Category;

    public function create(array $data): Category;

    public function update(int $id, array $data): Category;

    public function delete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
