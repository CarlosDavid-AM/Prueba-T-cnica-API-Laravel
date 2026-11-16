<?php

namespace App\Services;

use App\Exceptions\BusinessLogicException;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

class ProductService
{
    /**
     * Retrieve all products with their categories.
     *
     * @return Collection<int, Product>
     */
    public function getAll(): Collection
    {
        return Product::with('category')->get();
    }

    /**
     * Retrieve a product by its ID or throw a business exception.
     *
     * @throws BusinessLogicException
     */
    public function getById(int $id): Product
    {
        $product = Product::with('category')->find($id);

        if (! $product) {
            throw new BusinessLogicException("Producto con ID {$id} no encontrado", 404);
        }

        return $product;
    }

    /**
     * Create a new product.
     *
     * @param  array{category_id: int, name: string, description?: string|null, price: float|int|string, stock: int}  $data
     *
     * @throws BusinessLogicException
     */
    public function create(array $data): Product
    {
        if (isset($data['price']) && (float) $data['price'] < 0) {
            throw new BusinessLogicException('El precio del producto no puede ser negativo', 422);
        }

        if (isset($data['stock']) && (int) $data['stock'] < 0) {
            throw new BusinessLogicException('El stock del producto no puede ser negativo', 422);
        }

        if (isset($data['category_id']) && ! Category::where('id', $data['category_id'])->exists()) {
            throw new BusinessLogicException('La categoría especificada no existe', 422);
        }

        return Product::create($data);
    }

    /**
     * Update an existing product.
     *
     * @param  array{category_id?: int, name?: string, description?: string|null, price?: float|int|string, stock?: int}  $data
     *
     * @throws BusinessLogicException
     */
    public function update(int $id, array $data): Product
    {
        $product = $this->getById($id);

        if (isset($data['price']) && (float) $data['price'] < 0) {
            throw new BusinessLogicException('El precio del producto no puede ser negativo', 422);
        }

        if (isset($data['stock']) && (int) $data['stock'] < 0) {
            throw new BusinessLogicException('El stock del producto no puede ser negativo', 422);
        }

        if (isset($data['category_id']) && ! Category::where('id', $data['category_id'])->exists()) {
            throw new BusinessLogicException('La categoría especificada no existe', 422);
        }

        $product->update($data);

        return $product->fresh()->load('category');
    }

    /**
     * Delete a product by its ID.
     *
     * @throws BusinessLogicException
     */
    public function delete(int $id): void
    {
        $product = $this->getById($id);

        $product->delete();
    }
}
