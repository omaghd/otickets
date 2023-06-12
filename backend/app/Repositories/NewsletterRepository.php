<?php

namespace App\Repositories;

use App\Interfaces\NewsletterRepositoryInterface;
use App\Models\Newsletter;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class NewsletterRepository implements NewsletterRepositoryInterface
{
    public function getAll(): array
    {
        return Newsletter::query()
            ->search()
            ->latest()
            ->paginate(10)
            ->toArray();
    }

    public function create(array $data): Newsletter
    {
        return Newsletter::query()->create($data);
    }

    public function update(int $id, array $data): Newsletter
    {
        $newsletter = $this->find($id);
        $newsletter->update($data);

        return $newsletter;
    }

    public function find(int $id): Newsletter
    {
        try {
            return Newsletter::query()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Newsletter not found');
        }
    }

    public function delete(int $id): void
    {
        $newsletter = $this->find($id);
        $newsletter->delete();
    }
}
