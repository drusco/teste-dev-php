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

    public function all()
    {
        return Supplier::all();
    }

    public function find($id)
    {
        return Supplier::findOrFail($id);
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

    public function update($id, array $data)
    {
        $supplier = $this->find($id);
        $supplier->update($data);
        return $supplier;
    }

    public function delete($id)
    {
        $supplier = $this->find($id);
        return $supplier->delete();
    }
}
