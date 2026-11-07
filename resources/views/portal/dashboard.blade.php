@extends('layouts.portal')
@section('content')
<h1>Welcome, {{ $customer->name }}</h1>
<div class="card"><h2>Upcoming services</h2>
@forelse($upcoming as $b)<p>{{ $b->reference }} · {{ $b->scheduled_start }} · {{ $b->status }}</p>@empty<p>No upcoming bookings.</p>@endforelse
</div>
<div class="card"><h2>Recent invoices</h2>
@forelse($invoices as $i)<p>{{ $i->number }} · {{ $i->status }} · {{ $i->total }}</p>@empty<p>No invoices yet.</p>@endforelse
</div>
@endsection
