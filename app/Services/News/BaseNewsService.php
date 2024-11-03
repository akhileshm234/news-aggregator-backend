<?php

namespace App\Services\News;

use GuzzleHttp\Client;
use App\Exceptions\NewsApiException;

abstract class BaseNewsService
{
    protected Client $client;
    protected ?string $apiKey = null;
    protected ?string $baseUrl = null;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
        $this->validateConfig();
    }

    protected function makeRequest(string $endpoint, array $params = []): array
    {
        if (!$this->apiKey || !$this->baseUrl) {
            throw new NewsApiException('API configuration is incomplete');
        }

        try {
            $response = $this->client->get($this->baseUrl . $endpoint, [
                'query' => array_merge($params, ['api-key' => $this->apiKey])
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            throw new NewsApiException("API request failed: {$e->getMessage()}");
        }
    }

    abstract protected function validateConfig(): void;
    abstract public function fetch(array $parameters = []): array;
    abstract public function transform(array $articles): array;
} 