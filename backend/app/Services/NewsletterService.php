<?php

namespace App\Services;

use App\Interfaces\NewsletterRepositoryInterface;
use App\Models\Newsletter;

class NewsletterService
{
    public function __construct(protected NewsletterRepositoryInterface $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function create(array $data): Newsletter
    {
        $data['ip_address'] = $this->isAdmin() ? null : request()->ip();
        $data['user_agent'] = $this->isAdmin() ? null : request()->userAgent();

        return $this->repository->create($data);
    }

    private function isAdmin(): bool
    {
        return request()->user('sanctum') && request()->user('sanctum')->isAdmin();
    }

    public function update(int $id, array $data): Newsletter
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id): Newsletter
    {
        return $this->repository->find($id);
    }

    public function delete(int $id): void
    {
        $this->repository->delete($id);
    }
}
