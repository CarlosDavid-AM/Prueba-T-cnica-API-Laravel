# Gestión de Productos y Categorías (REST API)

Sistema backend diseñado como una API REST desacoplada y centralizada para la gestión completa (CRUD) de productos y categorías. Expone interfaces limpias y estandarizadas preparadas para ser consumidas por múltiples clientes (aplicaciones web, móviles o integraciones con partners externos).

---

## 1. Requisitos Previos

Antes de instalar y ejecutar la aplicación, asegúrese de contar con las siguientes herramientas instaladas en su entorno:

- **PHP**: Versión `>= 8.3` (con extensiones habilitadas: `pdo_sqlite`, `sqlite3`, `mbstring`, `openssl`, `curl`, `tokenizer`, `xml`, `ctype`).
- **Composer**: Versión `>= 2.x`.
- **SQLite**: Motor de base de datos local embebido.
- **Node.js y NPM** (Opcional, en caso de compilar assets front-end).

---

## 2. Comandos para Levantar el Entorno

Siga estos pasos para clonar, configurar e iniciar el proyecto:

### Paso 1: Clonar el repositorio y entrar al directorio

```bash
git clone https://github.com/CarlosDavid-AM/Prueba-T-cnica-API-Laravel.git
cd Prueba-T-cnica-API-Laravel
```

### Paso 2: Instalar las dependencias de Composer

```bash
composer install
```

### Paso 3: Configurar el archivo de entorno `.env`

Copie el archivo de ejemplo y genere la clave de aplicación:

```bash
# En Windows (PowerShell / CMD)
copy .env.example .env

# En Linux / macOS
cp .env.example .env

# Generar la APP_KEY
php artisan key:generate
```

Verifique que en su `.env` la conexión de base de datos apunte a SQLite:

```env
DB_CONNECTION=sqlite
```

### Paso 4: Crear la base de datos SQLite y ejecutar migraciones con Seeders

```bash
# Crear el archivo de base de datos SQLite si no existe
# En Windows (PowerShell):
if (!(Test-Path database/database.sqlite)) { New-Item -ItemType File database/database.sqlite }

# En Linux / macOS:
touch database/database.sqlite

# Ejecutar migraciones junto a los Seeders (carga 20 registros fake)
php artisan migrate --seed
```

### Paso 5: Generar la documentación interactiva Swagger

```bash
php artisan l5-swagger:generate
```

### Paso 6: Iniciar el servidor local de desarrollo

```bash
php artisan serve
```

El servidor estará escuchando por defecto en: `http://127.0.0.1:8000`.

---

## 3. URLs Base y Endpoints

- **URL Base del Servidor**: `http://127.0.0.1:8000`
- **Prefijo de la API REST**: `http://127.0.0.1:8000/api`
- **Documentación Interactiva (Swagger UI)**: `http://127.0.0.1:8000/api/documentation`

### Resumen de Endpoints Disponibles

#### Categorías

| Método   | Endpoint               | Acción    | Descripción                                  |
| -------- | ---------------------- | --------- | -------------------------------------------- |
| `GET`    | `/api/categories`      | `index`   | Listar todas las categorías (sin paginación) |
| `POST`   | `/api/categories`      | `store`   | Crear una nueva categoría                    |
| `GET`    | `/api/categories/{id}` | `show`    | Consultar el detalle de una categoría por ID |
| `PUT`    | `/api/categories/{id}` | `update`  | Actualizar una categoría existente           |
| `DELETE` | `/api/categories/{id}` | `destroy` | Eliminar una categoría (valida dependencias) |

#### Productos

| Método   | Endpoint             | Acción    | Descripción                                          |
| -------- | -------------------- | --------- | ---------------------------------------------------- |
| `GET`    | `/api/products`      | `index`   | Listar todos los productos con su categoría asociada |
| `POST`   | `/api/products`      | `store`   | Crear un nuevo producto                              |
| `GET`    | `/api/products/{id}` | `show`    | Consultar el detalle de un producto por ID           |
| `PUT`    | `/api/products/{id}` | `update`  | Actualizar un producto existente                     |
| `DELETE` | `/api/products/{id}` | `destroy` | Eliminar un producto por ID                          |

---

## 4. Estructura del Proyecto

El sistema adopta una arquitectura desacoplada donde la lógica de negocio se aísla de los controladores HTTP:

```text
app/
├── Exceptions/
│   └── BusinessLogicException.php    # Excepciones de negocio personalizadas con render JSON nativo
├── Http/
│   ├── Controllers/
│   │   └── Api/
│   │       ├── CategoryController.php # Controlador API delgado para Categorías (anotaciones OpenAPI)
│   │       ├── ProductController.php  # Controlador API delgado para Productos (anotaciones OpenAPI)
│   │       └── SwaggerController.php  # Metadatos globales y esquemas Swagger/OpenAPI
│   ├── Requests/
│   │   ├── BaseApiRequest.php         # Request base con respuesta JSON estandarizada para fallas de validación
│   │   ├── Category/
│   │   │   ├── StoreCategoryRequest.php
│   │   │   └── UpdateCategoryRequest.php
│   │   └── Product/
│   │       ├── StoreProductRequest.php
│   │       └── UpdateProductRequest.php
│   ├── Resources/
│   │   ├── CategoryResource.php       # Transformador y serializador de datos para Categorías
│   │   └── ProductResource.php        # Transformador y serializador de datos para Productos
│   └── Responses/
│       └── ApiResponse.php            # Helper centralizado para construir respuestas JSON consistentes
├── Models/
│   ├── Category.php                   # Modelo Eloquent con relación hasMany(Product::class)
│   └── Product.php                    # Modelo Eloquent con relación belongsTo(Category::class)
└── Services/
    ├── CategoryService.php            # Reglas de negocio y operaciones de persistencia de Categorías
    └── ProductService.php             # Reglas de negocio y operaciones de persistencia de Productos
database/
├── factories/
│   ├── CategoryFactory.php            # Fábrica de datos fake para Categorías
│   └── ProductFactory.php             # Fábrica de datos fake para Productos
├── migrations/
│   ├── 2026_11_16_164233_create_categories_table.php
│   └── 2026_11_16_164234_create_products_table.php
└── seeders/
    ├── CategorySeeder.php             # Seeder que genera 20 categorías fake
    ├── ProductSeeder.php              # Seeder que genera 20 productos fake asociados a categorías
    └── DatabaseSeeder.php             # Orquestador de seeders
```

---

## 5. Manejo de Errores y Respuestas Estándar

Todas las respuestas del API siguen una estructura JSON homogénea y predecible.

### 5.1. Respuesta Exitosa (`HTTP 200 / 201`)

Contiene siempre `success: true`, un `message` descriptivo y la carga útil en `data` (objeto o lista).

```json
{
    "success": true,
    "message": "Producto creado correctamente",
    "data": {
        "id": 1,
        "name": "Camiseta Overskull",
        "description": "Camiseta negra edición limitada",
        "price": 89.9,
        "stock": 50,
        "category_id": 1,
        "created_at": "2026-11-16T16:56:30.000000Z",
        "updated_at": "2026-11-16T16:56:30.000000Z"
    }
}
```

### 5.2. Error de Validación de Datos (`HTTP 422`)

Se produce cuando los datos enviados no cumplen las reglas de los Form Requests:

```json
{
    "success": false,
    "message": "El campo 'name' es obligatorio",
    "errors": {
        "name": ["El campo 'name' es obligatorio"]
    }
}
```

### 5.3. Recurso No Encontrado (`HTTP 404`)

Se produce al consultar, actualizar o eliminar un recurso inexistente:

```json
{
    "success": false,
    "message": "Producto con ID 999 no encontrado"
}
```

### 5.4. Excepción de Regla de Negocio (`HTTP 409 / 422`)

Se dispara ante violaciones a la lógica de negocio (por ejemplo, intentar eliminar una categoría que posee productos activos):

```json
{
    "success": false,
    "message": "No se puede eliminar la categoría porque tiene productos asociados"
}
```

---

## 6. Comandos Útiles

| Tarea                              | Comando                             |
| ---------------------------------- | ----------------------------------- |
| Iniciar servidor local             | `php artisan serve`                 |
| Listar todas las rutas del API     | `php artisan route:list --path=api` |
| Regenerar documentación Swagger    | `php artisan l5-swagger:generate`   |
| Reiniciar y repoblar base de datos | `php artisan migrate:fresh --seed`  |
| Formatear código con Laravel Pint  | `vendor/bin/pint`                   |
| Consola interactiva Tinker         | `php artisan tinker`                |
