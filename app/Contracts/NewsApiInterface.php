<?php

namespace App\Contracts;

interface NewsApiInterface
{
    public function fetch(): array;
    public function transform($article): array;
} 