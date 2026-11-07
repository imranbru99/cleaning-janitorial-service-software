<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function index()
    {
        return Invoice::query()->with('customer')->latest()->paginate(20);
    }

    public function show(Invoice $invoice)
    {
        return $invoice->load(['items', 'payments', 'customer']);
    }
}
