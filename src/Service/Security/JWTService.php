<?php

namespace App\Service\Security;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class JWTService
{
    public function decode(string $token, mixed $key, string $algorithm): object
    {
        return JWT::decode($token, new Key($key, $algorithm));
    }

    public function encode(array $payload, mixed $key, string $algorithm): string
    {
        return JWT::encode($payload, $key, $algorithm);
    }
}
