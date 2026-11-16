<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\StoreProductRequest;
use App\Http\Requests\Product\UpdateProductRequest;
use App\Http\Resources\ProductResource;
use App\Http\Responses\ApiResponse;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class ProductController extends Controller
{
    public function __construct(
        protected ProductService $productService
    ) {}

    #[OA\Get(
        path: '/products',
        summary: 'Listar todos los productos',
        description: 'Retorna la lista completa de productos registrados, incluyendo la información de su categoría.',
        tags: ['Productos'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de productos obtenida con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Productos obtenidos correctamente'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/ProductSchema')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $products = $this->productService->getAll();

        return ApiResponse::success(
            data: ProductResource::collection($products),
            message: 'Productos obtenidos correctamente'
        );
    }

    #[OA\Post(
        path: '/products',
        summary: 'Crear un nuevo producto',
        description: 'Registra un nuevo producto en el sistema asociado a una categoría.',
        tags: ['Productos'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name', 'price', 'stock', 'category_id'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Camiseta negra edición limitada'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.90),
                    new OA\Property(property: 'stock', type: 'integer', example: 50),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Producto creado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto creado correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductSchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación o lógica de negocio',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function store(StoreProductRequest $request): JsonResponse
    {
        $product = $this->productService->create($request->validated());

        return ApiResponse::success(
            data: new ProductResource($product),
            message: 'Producto creado correctamente',
            status: 201
        );
    }

    #[OA\Get(
        path: '/products/{id}',
        summary: 'Obtener un producto por ID',
        description: 'Retorna los detalles de un producto específico.',
        tags: ['Productos'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID del producto',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalle del producto obtenido con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto obtenido correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductSchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $product = $this->productService->getById($id);

        return ApiResponse::success(
            data: new ProductResource($product),
            message: 'Producto obtenido correctamente'
        );
    }

    #[OA\Put(
        path: '/products/{id}',
        summary: 'Actualizar un producto',
        description: 'Actualiza los datos de un producto existente.',
        tags: ['Productos'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID del producto',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull V2'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Camiseta negra edición limitada renovada'),
                    new OA\Property(property: 'price', type: 'number', format: 'float', example: 99.90),
                    new OA\Property(property: 'stock', type: 'integer', example: 40),
                    new OA\Property(property: 'category_id', type: 'integer', example: 1),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto actualizado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto actualizado correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/ProductSchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación o datos inválidos',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function update(UpdateProductRequest $request, int $id): JsonResponse
    {
        $product = $this->productService->update($id, $request->validated());

        return ApiResponse::success(
            data: new ProductResource($product),
            message: 'Producto actualizado correctamente'
        );
    }

    #[OA\Delete(
        path: '/products/{id}',
        summary: 'Eliminar un producto',
        description: 'Elimina un producto del catálogo.',
        tags: ['Productos'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID del producto',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Producto eliminado con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Producto eliminado correctamente'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Producto no encontrado',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->productService->delete($id);

        return ApiResponse::success(
            message: 'Producto eliminado correctamente'
        );
    }
}
