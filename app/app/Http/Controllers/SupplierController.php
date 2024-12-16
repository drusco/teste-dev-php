<?php

namespace App\Http\Controllers;

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
        try {

            // validate the incoming request
            $data = $request->validate([
                'name' => 'required_if:document,11|string|max:255',
                'address' => 'required_if:document,11|string|max:255',
                'email' => 'required|string|email|max:255',
                'phone' => 'nullable|string|max:20',
                'document' => [
                    'required', 
                    'string', 
                    'regex:/^\d{11}$|^\d{14}$/',
                    'unique:suppliers,document'
                ],
            ]);

            $document = $data['document'];

            // Find CNPJ data using an external service
            if (strlen($document) == 14) {
                $cnpj = $this->supplierRepository->findCnpjData($document);

                if(!$cnpj) {
                    return response()->json([
                        'message' => 'The document is not a valid CNPJ',
                        'errors' => ['supplier' => ['An error occurred while creating the supplier.']],
                    ], 500);
                }

                // Add name and address from the external api
                $data['name'] = $cnpj['name'];
                $data['address'] = $cnpj['address'];
                
                // If no phone is provided use the one from the external api
                if(!isset($data['phone'])) {
                    $data['phone'] = $cnpj['phone'];
                }

            }
    
            // Attempt to create the supplier
            $supplier = $this->supplierRepository->create($data);
    
            // Return success response if supplier is created
            return response()->json($supplier, 201);
        } catch (Exception $e) {
            // Log the error message
            \Log::error('An error occurred while creating the supplier.', [
                'exception' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            // Handle custom error response
            return response()->json([
                'message' => 'An unexpected error occurred.',
                'errors' => ['supplier' => ['An error occurred while creating the supplier.']],
            ], 500);
        }
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
