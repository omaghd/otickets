<?php

namespace App\Repositories;

use App\Builders\FaqBuilder;
use App\Interfaces\FaqRepositoryInterface;
use App\Models\Faq;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class FaqRepository implements FaqRepositoryInterface
{
    public function getAll(): array
    {
        return Faq::query()
            ->filterByCategory()
            ->filterByDates()
            ->search()
            ->withCategory()
            ->when(
                $this->isAdmin(),
                fn(FaqBuilder $query) => $query->getTrashed(),
                fn(FaqBuilder $query) => $query->published()
            )
            ->latest()
            ->paginate(10)
            ->toArray();
    }

    private function isAdmin(): bool
    {
        return request()->user('sanctum') && request()->user('sanctum')->isAdmin();
    }

    public function getBySlug(string $slug): Faq
    {
        try {
            return Faq::query()
                ->published()
                ->getBySlug($slug)
                ->when(
                    request('home') == 'true',
                    fn(FaqBuilder $query) => $query->withFaqs(),
                    fn(FaqBuilder $query) => $query->withCategory()
                )
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Faq not found');
        }
    }

    public function getSuggestions(string $query): array
    {
        return Faq::query()
            ->search($query)
            ->published()
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function create(array $data): Faq
    {
        return Faq::create($data);
    }

    public function update(int $id, array $data): Faq
    {
        $faq = $this->find($id);
        $faq->update($data);

        return $faq;
    }

    public function find(int $id): Faq
    {
        try {
            return Faq::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Faq not found');
        }
    }

    public function delete(int $id): void
    {
        $faq = $this->find($id);
        $faq->delete();
    }

    public function restore(int $id): void
    {
        $faq = $this->findInTrash($id);
        $faq->restore();
    }

    public function findInTrash(int $id): Faq
    {
        try {
            return Faq::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Faq not found');
        }
    }

    public function forceDelete(int $id): void
    {
        $faq = $this->findInTrash($id);
        $faq->forceDelete();
    }
}
