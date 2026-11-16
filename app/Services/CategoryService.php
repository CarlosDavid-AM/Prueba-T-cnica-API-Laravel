<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Models\Category;
use Illuminate\Database\Eloquent\Collection;

class CategoryService
{
    /**
     * Retrieve all categories.
     *
     * @return Collection<int, Category>
     */
    public function getAll(): Collection
    {
        return Category::all();
    }

    /**
     * Retrieve a category by its ID or throw a business exception.
     *
     * @throws BusinessLogicException
     */
    public function getById(int $id): Category
    {
        $category = Category::find($id);

        if (! $category) {
            throw new BusinessLogicException("Categoría con ID {$id} no encontrada", 404);
        }

        return $category;
    }

    /**
     * Create a new category.
     *
     * @param  array{name: string, description?: string|null}  $data
     */
    public function create(array $data): Category
    {
        return Category::create($data);
    }

    /**
     * Update an existing category.
     *
     * @param  array{name?: string, description?: string|null}  $data
     *
     * @throws BusinessLogicException
     */
    public function update(int $id, array $data): Category
    {
        $category = $this->getById($id);

        $category->update($data);

        return $category->fresh();
    }

    /**
     * Delete a category verifying business constraints.
     *
     * @throws BusinessLogicException
     */
    public function delete(int $id): void
    {
        $category = $this->getById($id);

        if ($category->products()->exists()) {
            throw new BusinessLogicException('No se puede eliminar la categoría porque tiene productos asociados', 409);
        }

        $category->delete();
    }
}
