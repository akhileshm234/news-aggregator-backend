<?php

namespace App\Virtual\Resources;

/**
 * @OA\Schema(
 *     title="AuthResource",
 *     description="Auth response resource",
 *     @OA\Xml(name="AuthResource")
 * )
 */
class AuthResource
{
    /**
     * @OA\Property(type="string", example="1|abcdef123456")
     */
    private $token;

    /**
     * @OA\Property(ref="#/components/schemas/User")
     */
    private $user;
} 