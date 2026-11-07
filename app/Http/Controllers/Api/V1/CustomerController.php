<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::query()->latest()->paginate(20);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:40',
            'type' => 'nullable|string',
        ]);

        return response()->json(Customer::query()->create($data), 201);
    }

    public function show(Customer $customer)
    {
        return $customer->load(['contacts', 'addresses']);
    }

    public function update(Request $request, Customer $customer)
    {
        $customer->update($request->validate([
            'name' => 'sometimes|string|max:160',
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:40',
            'status' => 'nullable|string',
        ]));

        return $customer->fresh();
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
