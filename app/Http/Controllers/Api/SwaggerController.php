<?php

namespace App\Http\Controllers\Api;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: '1.0.0',
    title: 'API de Gestión de Productos y Categorías',
    description: 'Documentación de la API REST para el sistema centralizado de productos y categorías. Expuesta para clientes web, mobile y partners externos.'
)]
#[OA\Server(
    url: '/api',
    description: 'Servidor API Local'
)]
#[OA\Tag(
    name: 'Categorías',
    description: 'Endpoints para la gestión de categorías'
)]
#[OA\Tag(
    name: 'Productos',
    description: 'Endpoints para la gestión de productos'
)]
#[OA\Schema(
    schema: 'StandardErrorResponse',
    type: 'object',
    properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false),
        new OA\Property(property: 'message', type: 'string', example: "El campo 'name' es obligatorio"),
        new OA\Property(
            property: 'errors',
            type: 'object',
            nullable: true,
            example: ['name' => ['El campo nombre es requerido']]
        ),
    ]
)]
#[OA\Schema(
    schema: 'CategorySchema',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Ropa'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Prendas de vestir y accesorios'),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-11-16T12:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-11-16T12:00:00.000000Z'),
    ]
)]
#[OA\Schema(
    schema: 'ProductSchema',
    type: 'object',
    properties: [
        new OA\Property(property: 'id', type: 'integer', example: 1),
        new OA\Property(property: 'name', type: 'string', example: 'Camiseta Overskull'),
        new OA\Property(property: 'description', type: 'string', nullable: true, example: 'Camiseta negra edición limitada'),
        new OA\Property(property: 'price', type: 'number', format: 'float', example: 89.90),
        new OA\Property(property: 'stock', type: 'integer', example: 50),
        new OA\Property(property: 'category_id', type: 'integer', example: 1),
        new OA\Property(property: 'created_at', type: 'string', format: 'date-time', example: '2026-11-16T12:00:00.000000Z'),
        new OA\Property(property: 'updated_at', type: 'string', format: 'date-time', example: '2026-11-16T12:00:00.000000Z'),
    ]
)]
class SwaggerController {}
