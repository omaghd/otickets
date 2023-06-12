<?php

namespace App\Interfaces;

use App\Models\CannedResponse;

interface CannedResponseRepositoryInterface
{
    public function getAll(): array;

    public function find(int $id): CannedResponse;

    public function findInTrash(int $id): CannedResponse;

    public function create(array $data): CannedResponse;

    public function update(int $id, array $data): CannedResponse;

    public function delete(int $id): void;

    public function restore(int $id): void;

    public function forceDelete(int $id): void;
}
