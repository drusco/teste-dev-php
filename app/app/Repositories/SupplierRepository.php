<?php

namespace App\Repositories;

use App\Models\Supplier;
use GuzzleHttp\Exception\RequestException;
use App\Repositories\ExternalApiRepository;

class SupplierRepository implements SupplierRepositoryInterface
{

    protected $externalApiRepository;

    public function __construct(ExternalApiRepositoryInterface $externalApiRepository)
    {
        $this->externalApiRepository = $externalApiRepository;
    }

    public function all($filters = [], $orderBy = 'id', $orderDirection = 'asc', $perPage = 15)
    {
        $query = Supplier::query();

        // Apply filters
        foreach ($filters as $filter => $value) {
            $query->where($filter, 'like', "%{$value}%");
        }

        // Apply ordering
        $query->orderBy($orderBy, $orderDirection);

        // Apply pagination
        return $query->paginate($perPage);
    }

    public function find($document)
    {
        $supplier = Supplier::where('document', $document)->first();

        if(!$supplier) {
            return null;
        }

        return $supplier;
    }

    public function findCnpjData(string $cnpj): ?array {
        $supplier = $this->externalApiRepository->fetchCNPJ($cnpj);

        if(!$supplier) {
            return null;
        }

        return [
            'name'      => $supplier['razao_social'],
            'phone'     => $supplier['ddd_telefone_1'],
            'address'   => "{$supplier['logradouro']}, {$supplier['complemento']}, {$supplier['bairro']} ({$supplier['cep']}), {$supplier['municipio']} - {$supplier['uf']}",
            'document'  => $cnpj,
        ];
    }

    public function create(array $data)
    {
        return Supplier::create($data);
    }

    public function update($document, array $data)
    {
        // Find the supplier by document
        $supplier = Supplier::where('document', $document)->first();
    
        // If no supplier is found, return an error or handle it
        if (!$supplier) {
            return null;
        }

        // Update the supplier and return its data
        $supplier->update($data);
    
        return $supplier;
    }
    

    public function delete($document)
    {
        $supplier = Supplier::where('document', $document)->first();

        if(!$supplier) {
            return null;
        }

        return $supplier->delete();
    }
}
