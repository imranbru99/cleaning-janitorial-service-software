@extends('layouts.website')
@section('title', $module['name'].' · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>{{ $module['name'] }}</h2>
    <p class="lead">{{ $module['description'] }}</p>
    <div class="item">
        <p>Version {{ $module['version'] ?? '1.0.0' }} · Category {{ $module['category'] ?? 'general' }}</p>
        <p>Dependencies: {{ empty($module['dependencies']) ? 'None' : implode(', ', $module['dependencies']) }}</p>
    </div>
</section>
@endsection
