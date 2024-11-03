<?php

namespace App\Contracts;

interface NewsApiInterface
{
    public function fetch(array $parameters = []): array;
    public function transform(array $articles): array;
} 