<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Category\StoreCategoryRequest;
use App\Http\Requests\Category\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Http\Responses\ApiResponse;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;
use OpenApi\Attributes as OA;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService
    ) {}

    #[OA\Get(
        path: '/categories',
        summary: 'Listar todas las categorías',
        description: 'Retorna la lista completa de categorías registradas.',
        tags: ['Categorías'],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Lista de categorías obtenida con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categorías obtenidas correctamente'),
                        new OA\Property(
                            property: 'data',
                            type: 'array',
                            items: new OA\Items(ref: '#/components/schemas/CategorySchema')
                        ),
                    ]
                )
            ),
        ]
    )]
    public function index(): JsonResponse
    {
        $categories = $this->categoryService->getAll();

        return ApiResponse::success(
            data: CategoryResource::collection($categories),
            message: 'Categorías obtenidas correctamente'
        );
    }

    #[OA\Post(
        path: '/categories',
        summary: 'Crear una nueva categoría',
        description: 'Registra una nueva categoría en el sistema.',
        tags: ['Categorías'],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['name'],
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ropa'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Prendas de vestir y accesorios'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 201,
                description: 'Categoría creada con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría creada correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategorySchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $this->categoryService->create($request->validated());

        return ApiResponse::success(
            data: new CategoryResource($category),
            message: 'Categoría creada correctamente',
            status: 201
        );
    }

    #[OA\Get(
        path: '/categories/{id}',
        summary: 'Obtener una categoría por ID',
        description: 'Retorna la información detallada de una categoría específica.',
        tags: ['Categorías'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID de la categoría',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Detalle de la categoría obtenido con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría obtenida correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategorySchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Categoría no encontrada',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function show(int $id): JsonResponse
    {
        $category = $this->categoryService->getById($id);

        return ApiResponse::success(
            data: new CategoryResource($category),
            message: 'Categoría obtenida correctamente'
        );
    }

    #[OA\Put(
        path: '/categories/{id}',
        summary: 'Actualizar una categoría',
        description: 'Actualiza los datos de una categoría existente.',
        tags: ['Categorías'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID de la categoría',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(property: 'name', type: 'string', example: 'Ropa de Moda'),
                    new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Prendas de vestir actualizadas'),
                ]
            )
        ),
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría actualizada con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría actualizada correctamente'),
                        new OA\Property(property: 'data', ref: '#/components/schemas/CategorySchema'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Categoría no encontrada',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
            new OA\Response(
                response: 422,
                description: 'Error de validación',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function update(UpdateCategoryRequest $request, int $id): JsonResponse
    {
        $category = $this->categoryService->update($id, $request->validated());

        return ApiResponse::success(
            data: new CategoryResource($category),
            message: 'Categoría actualizada correctamente'
        );
    }

    #[OA\Delete(
        path: '/categories/{id}',
        summary: 'Eliminar una categoría',
        description: 'Elimina una categoría del sistema siempre que no tenga productos asociados.',
        tags: ['Categorías'],
        parameters: [
            new OA\PathParameter(
                name: 'id',
                description: 'ID de la categoría',
                required: true,
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Categoría eliminada con éxito',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'success', type: 'boolean', example: true),
                        new OA\Property(property: 'message', type: 'string', example: 'Categoría eliminada correctamente'),
                    ]
                )
            ),
            new OA\Response(
                response: 404,
                description: 'Categoría no encontrada',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
            new OA\Response(
                response: 409,
                description: 'Conflicto: la categoría posee productos asociados',
                content: new OA\JsonContent(ref: '#/components/schemas/StandardErrorResponse')
            ),
        ]
    )]
    public function destroy(int $id): JsonResponse
    {
        $this->categoryService->delete($id);

        return ApiResponse::success(
            message: 'Categoría eliminada correctamente'
        );
    }
}
