<?php

namespace App\Interfaces;

use App\Models\Faq;

interface FaqRepositoryInterface
{
    public function getAll(): array;

    public function getBySlug(string $slug): Faq;

    public function getSuggestions(string $query): array;

    public function find(int $id): Faq;

    public function findInTrash(int $id): Faq;

    public function create(array $data): Faq;

    public function update(int $id, array $data): Faq;

    public function delete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
