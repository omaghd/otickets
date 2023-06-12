<?php

namespace App\Repositories;

use App\Builders\CategoryBuilder;
use App\Interfaces\CategoryRepositoryInterface;
use App\Models\Category;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CategoryRepository implements CategoryRepositoryInterface
{
    public function getAll(): array
    {
        return Category::query()
            ->filterByDepartment()
            ->search()
            ->when(
                $this->isManager(),
                fn(CategoryBuilder $query) => $query
                    ->getTrashed()
                    ->withDepartment()
                    ->withDepartment()
                    ->withTicketsCount()
                    ->withCannedResponsesCount()
                    ->withFaqsCount(),
                fn(CategoryBuilder $query) => $query->withFaqsCount(onlyPublished: true)
            )
            ->latest()
            ->paginateOrGet();
    }

    private function isManager(): bool
    {
        return request()->user('sanctum') && request()->user('sanctum')->isManager();
    }

    public function countRelatedTickets(): array
    {
        return Category::query()
            ->whereHasTickets()
            ->withTicketsCount()
            ->latest()
            ->get()
            ->map(fn(Category $category) => [
                'name'  => $category->name,
                'count' => $category->tickets_count
            ])
            ->toArray();
    }

    public function getBySlug(string $slug): Category
    {
        try {
            return Category::query()
                ->withFaqs()
                ->getBySlug($slug)
                ->firstOrFail();
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Category not found');
        }
    }

    public function create(array $data): Category
    {
        return Category::create($data);
    }

    public function update(int $id, array $data): Category
    {
        $category = $this->find($id);
        $category->update($data);

        return $category;
    }

    public function find(int $id): Category
    {
        try {
            return Category::findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Category not found');
        }
    }

    public function delete(int $id): void
    {
        $category = $this->find($id);
        $category->delete();
    }

    public function restore(int $id): void
    {
        $category = $this->findInTrash($id);
        $category->restore();
    }

    public function findInTrash(int $id): Category
    {
        try {
            return Category::onlyTrashed()->findOrFail($id);
        } catch (ModelNotFoundException) {
            throw new ModelNotFoundException('Category not found');
        }
    }

    public function forceDelete(int $id): void
    {
        $category = $this->findInTrash($id);
        $category->forceDelete();
    }
}
