@extends('layouts.website')
@section('title', config('cleaning.name'))
@section('content')
<section class="wrap hero">
    <div>
        <div class="brand" style="font-size:1.6rem;margin-bottom:.8rem">{{ config('cleaning.short_name') }}</div>
        <h1>Run cleaning operations with precision.</h1>
        <p>Scheduling, workforce, quality inspections, invoicing, and analytics for residential cleaners and global janitorial enterprises.</p>
        <div class="cta-row">
            <a class="btn btn-primary" href="{{ route('trial') }}">Get Started</a>
            <a class="btn btn-secondary" href="{{ route('demo') }}">Book a Demo</a>
        </div>
    </div>
    <div class="preview">
        <h3>Operations Dashboard</h3>
        <p>Live bookings, crew assignments, inspection scores, and revenue in one command center.</p>
    </div>
</section>

<section class="section wrap">
    <h2>Built for every cleaning vertical</h2>
    <p class="lead">Activate only the modules your business needs — from simple bookings to franchise-scale quality assurance.</p>
    <div class="grid">
        @foreach($modules as $module)
            <div class="item">
                <h3>{{ $module['name'] }}</h3>
                <p>{{ $module['description'] }}</p>
            </div>
        @endforeach
    </div>
</section>

<section class="section wrap">
    <h2>Trusted outcomes</h2>
    <div class="stats">
        <div class="stat"><strong>40+</strong> modular capabilities</div>
        <div class="stat"><strong>15+</strong> industry solutions</div>
        <div class="stat"><strong>RTL</strong> multilingual PDFs</div>
        <div class="stat"><strong>API</strong> ready integrations</div>
    </div>
</section>

<section class="section wrap">
    <h2>What operators say</h2>
    <div class="grid">
        @forelse($testimonials as $t)
            <div class="item">
                <h3>{{ $t->customer_name }}@if($t->company) · {{ $t->company }}@endif</h3>
                <p>“{{ $t->quote }}”</p>
            </div>
        @empty
            <div class="item"><h3>Northline Facilities</h3><p>“CleanOps replaced three tools and raised our inspection pass rate in one quarter.”</p></div>
            <div class="item"><h3>BrightHome Cleaning</h3><p>“Dispatchers finally see conflicts before they become missed appointments.”</p></div>
        @endforelse
    </div>
</section>

<section class="section wrap">
    <h2>Plans that scale with your crews</h2>
    <p class="lead">Start with booking and invoicing, then unlock inspections, attendance, payroll, and franchise controls.</p>
    <div class="cta-row">
        <a class="btn btn-primary" href="{{ route('pricing') }}">View Pricing</a>
        <a class="btn btn-secondary" href="{{ route('faq') }}">Read FAQ</a>
    </div>
</section>
@endsection
