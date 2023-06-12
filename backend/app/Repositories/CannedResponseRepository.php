<?php

namespace App\Repositories;

use App\Interfaces\CannedResponseRepositoryInterface;
use App\Models\CannedResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CannedResponseRepository implements CannedResponseRepositoryInterface
{
    public function getAll(): array
    {
        return CannedResponse::query()
            ->getTrashed()
            ->getOwn()
            ->filterByAgent()
            ->filterByCategory()
            ->search()
            ->withCategory()
            ->withAgent()
            ->latest()
            ->paginateOrGet();
    }

    public function create(array $data): CannedResponse
    {
        return CannedResponse::create($data);
    }

    public function update(int $id, array $data): CannedResponse
    {
        $cannedResponse = $this->find($id);
        $cannedResponse->update($data);

        return $cannedResponse;
    }

    public function find(int $id): CannedResponse
    {
        try {
            return CannedResponse::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('CannedResponse not found');
        }
    }

    public function delete(int $id): void
    {
        $cannedResponse = $this->find($id);
        $cannedResponse->delete();
    }

    public function restore(int $id): void
    {
        $cannedResponse = $this->findInTrash($id);
        $cannedResponse->restore();
    }

    public function findInTrash(int $id): CannedResponse
    {
        try {
            return CannedResponse::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('CannedResponse not found');
        }
    }

    public function forceDelete(int $id): void
    {
        $cannedResponse = $this->findInTrash($id);
        $cannedResponse->forceDelete();
    }
}
