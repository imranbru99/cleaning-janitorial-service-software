@extends('layouts.website')
@section('title', 'Pricing · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>Subscription plans</h2>
    <p class="lead">Configurable limits for employees, branches, bookings, storage, and AI usage.</p>
    <div class="grid">
        @forelse($plans as $plan)
            <div class="item">
                <h3>{{ $plan->name }}</h3>
                <p>${{ number_format($plan->price_monthly, 0) }}/mo · ${{ number_format($plan->price_yearly, 0) }}/yr</p>
                <p>{{ $plan->description }}</p>
                <a class="btn btn-primary" href="{{ route('trial', ['plan' => $plan->slug]) }}">Choose {{ $plan->name }}</a>
            </div>
        @empty
            <div class="item"><h3>Starter</h3><p>CRM, bookings, employees, invoicing.</p></div>
            <div class="item"><h3>Professional</h3><p>Inspections, contracts, inventory, portal.</p></div>
            <div class="item"><h3>Business</h3><p>Branches, attendance, payroll, automation.</p></div>
            <div class="item"><h3>Enterprise</h3><p>Franchise, API, AI, advanced permissions.</p></div>
        @endforelse
    </div>
</section>
@endsection
