<?php

namespace App\Exceptions;

use App\Http\Responses\ApiResponse;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BusinessLogicException extends Exception
{
    /**
     * @param  array<string, mixed>|null  $errors
     */
    public function __construct(
        string $message = 'Error en la lógica del negocio',
        int $code = 400,
        protected ?array $errors = null
    ) {
        parent::__construct($message, $code);
    }

    /**
     * Get the associated validation or error details.
     *
     * @return array<string, mixed>|null
     */
    public function getErrors(): ?array
    {
        return $this->errors;
    }

    /**
     * Render the exception into an HTTP response.
     */
    public function render(Request $request): JsonResponse
    {
        $status = ($this->getCode() >= 400 && $this->getCode() < 600) ? $this->getCode() : 400;

        return ApiResponse::error(
            message: $this->getMessage(),
            errors: $this->errors,
            status: $status
        );
    }
}
