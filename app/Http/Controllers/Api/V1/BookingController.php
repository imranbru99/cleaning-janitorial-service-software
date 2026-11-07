<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Services\Booking\BookingService;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index()
    {
        return Booking::query()->with(['customer', 'items'])->latest()->paginate(20);
    }

    public function store(Request $request, BookingService $bookings)
    {
        $data = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'customer_address_id' => 'nullable|exists:customer_addresses,id',
            'scheduled_start' => 'nullable|date',
            'scheduled_end' => 'nullable|date|after:scheduled_start',
            'special_instructions' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.service_id' => 'nullable|exists:services,id',
            'items.*.quantity' => 'nullable|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $booking = $bookings->create($data, $data['items']);

        return response()->json($booking, 201);
    }

    public function show(Booking $booking)
    {
        return $booking->load(['customer', 'items', 'cleaningJob']);
    }

    public function update(Request $request, Booking $booking)
    {
        $booking->update($request->validate([
            'status' => 'sometimes|string',
            'scheduled_start' => 'nullable|date',
            'scheduled_end' => 'nullable|date',
            'special_instructions' => 'nullable|string',
        ]));

        return $booking->fresh();
    }

    public function destroy(Booking $booking)
    {
        $booking->delete();

        return response()->json(['message' => 'Deleted']);
    }
}
