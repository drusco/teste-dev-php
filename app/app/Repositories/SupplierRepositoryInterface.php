<?php

namespace App\Repositories;

interface SupplierRepositoryInterface
{
    public function all();
    public function find($id);
    public function findCnpjData(string $cnpj): ?array;
    public function create(array $data);
    public function update($document, array $data);
    public function delete($id);
}
