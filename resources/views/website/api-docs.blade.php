@extends('layouts.website')
@section('title', 'API Documentation')
@section('content')
<section class="wrap section">
    <h2>API v1</h2>
    <p class="lead">Authenticate with Sanctum bearer tokens. All tenant-owned endpoints are scoped to the authenticated user tenant.</p>
    <div class="item"><h3>POST /api/v1/login</h3><p>Exchange email/password for a token.</p></div>
    <div class="item"><h3>GET /api/v1/customers</h3><p>Requires module:crm</p></div>
    <div class="item"><h3>POST /api/v1/bookings</h3><p>Requires module:bookings</p></div>
    <div class="item"><h3>GET /api/v1/jobs</h3><p>Requires module:jobs</p></div>
    <div class="item"><h3>POST /api/v1/inspections</h3><p>Requires module:quality</p></div>
</section>
@endsection
