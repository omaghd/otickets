<?php

namespace App\Interfaces;

use App\Models\Newsletter;

interface NewsletterRepositoryInterface
{
    public function getAll(): array;

    public function find(int $id): Newsletter;

    public function create(array $data): Newsletter;

    public function update(int $id, array $data): Newsletter;

    public function delete(int $id): void;
}
