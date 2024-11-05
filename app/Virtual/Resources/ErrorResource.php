<?php

namespace App\Virtual\Resources;

/**
 * @OA\Schema(
 *     title="ErrorResource",
 *     description="Error response resource",
 *     @OA\Xml(name="ErrorResource")
 * )
 */
class ErrorResource
{
    /**
     * @OA\Property(type="string", example="The given data was invalid.")
     */
    private $message;

    /**
     * @OA\Property(type="integer", example=422)
     */
    private $status_code;

    /**
     * @OA\Property(
     *     property="errors",
     *     type="object",
     *     @OA\AdditionalProperties(
     *         type="array",
     *         @OA\Items(type="string")
     *     )
     * )
     */
    private $errors;
} 