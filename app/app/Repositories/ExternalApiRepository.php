<?php

namespace App\Repositories;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class ExternalApiRepository implements ExternalApiRepositoryInterface
{
    protected $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Finds CNPJ data using an external API.
     *
     * @param string $cnpj
     * @return array|null
     */
    public function fetchCNPJ(string $cnpj): ?array
    {
        try {
            $api_url = env('CNPJ_EXTERNAL_API');

            $response = $this->client->get("{$api_url}/{$cnpj}");

            $responseData = json_decode($response->getBody()->getContents(), true);

            return $responseData;
        } catch (RequestException $e) {

            // Log the error or handle it as needed
            \Log::error('CNPJ API error: ', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return null;
        }
    }
}
