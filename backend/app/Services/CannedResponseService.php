<?php

namespace App\Services;

use App\Interfaces\CannedResponseRepositoryInterface;
use App\Models\CannedResponse;

class CannedResponseService
{
    public function __construct(protected CannedResponseRepositoryInterface $repository)
    {
    }

    public function getAll(): array
    {
        return $this->repository->getAll();
    }

    public function create(array $data): CannedResponse
    {
        if (request()->user('sanctum')->isAgent())
            $data['agent_id'] = request()->user('sanctum')->id;

        return $this->repository->create($data);
    }

    public function update(int $id, array $data): CannedResponse
    {
        return $this->repository->update($id, $data);
    }

    public function find(int $id): CannedResponse
    {
        return $this->repository->find($id);
    }

    public function findInTrash(int $id): CannedResponse
    {
        return $this->repository->findInTrash($id);
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
