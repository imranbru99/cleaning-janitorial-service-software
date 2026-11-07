<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Inspection;
use App\Models\Invoice;
use Illuminate\Support\Facades\Auth;

class PortalController extends Controller
{
    public function dashboard()
    {
        $customer = Auth::user()?->customer;
        abort_unless($customer, 403);

        return view('portal.dashboard', [
            'customer' => $customer,
            'upcoming' => Booking::query()->where('customer_id', $customer->id)->where('scheduled_start', '>=', now())->orderBy('scheduled_start')->take(5)->get(),
            'invoices' => Invoice::query()->where('customer_id', $customer->id)->latest()->take(5)->get(),
        ]);
    }

    public function bookings()
    {
        $customer = Auth::user()?->customer;
        abort_unless($customer, 403);

        return view('portal.bookings', [
            'bookings' => Booking::query()->where('customer_id', $customer->id)->latest()->paginate(15),
        ]);
    }

    public function invoices()
    {
        $customer = Auth::user()?->customer;
        abort_unless($customer, 403);

        return view('portal.invoices', [
            'invoices' => Invoice::query()->where('customer_id', $customer->id)->latest()->paginate(15),
        ]);
    }

    public function inspections()
    {
        $customer = Auth::user()?->customer;
        abort_unless($customer, 403);

        return view('portal.inspections', [
            'inspections' => Inspection::query()->where('customer_id', $customer->id)->latest()->paginate(15),
        ]);
    }
}
