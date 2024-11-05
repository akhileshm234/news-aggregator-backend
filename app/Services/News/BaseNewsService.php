<?php

namespace App\Services\News;

use App\Contracts\NewsApiInterface;
use App\Exceptions\NewsApiException;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

abstract class BaseNewsService implements NewsApiInterface
{
    protected string $baseUrl;
    protected ?string $apiKey;
    protected array $headers;

    public function __construct()
    {
        $this->headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json'
        ];
    }

    protected function get(string $endpoint, array $params = []): array
    {
        try {
            $response = Http::withHeaders($this->headers)
                ->get($this->baseUrl . $endpoint, $params);

            if ($response->successful()) {
                return $response->json();
            }

            throw new NewsApiException(
                "API request failed: " . $response->body(),
                $response->status()
            );
        } catch (\Exception $e) {
            Log::error('News API request failed', [
                'service' => static::class,
                'endpoint' => $endpoint,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

    abstract public function fetch(): array;
    abstract public function transform($article): array;
} 