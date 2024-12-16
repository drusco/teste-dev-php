<?php

namespace App\Repositories;

interface ExternalApiRepositoryInterface
{
    /**
     * Finds CNPJ data using an external API.
     *
     * @param string $cnpj
     * @return array|null
     */
    public function fetchCNPJ(string $cnpj): ?array;
}
