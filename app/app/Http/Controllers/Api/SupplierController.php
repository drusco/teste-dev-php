<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\SupplierRepositoryInterface;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    private $supplierRepository;

    public function __construct(SupplierRepositoryInterface $supplierRepository)
    {
        $this->supplierRepository = $supplierRepository;
    }

    public function index()
    {
        return response()->json($this->supplierRepository->all());
    }

    public function show($id)
    {
        return response()->json($this->supplierRepository->find($id));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required_if:document,11|string|max:14',
            'address' => 'required_if:document,11|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:20',
            'document' => [
                'required', 
                'string', 
                'regex:/^\d{11}$|^\d{14}$/'
            ],
        ]);

        return response()->json($this->supplierRepository->create($data), 201);
    }

    public function update(Request $request, $id)
    {
        $data = $request->validate([
            'name' => 'required_if:document,11|string|max:255',
            'address' => 'required_if:document,11|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'nullable|string|max:20'
        ]);

        return response()->json($this->supplierRepository->update($id, $data));
    }

    public function destroy($id)
    {
        $this->supplierRepository->delete($id);
        return response()->json(null, 204);
    }
}
