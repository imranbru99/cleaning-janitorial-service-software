@extends('layouts.website')
@section('title', 'Features · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>Platform features</h2>
    <p class="lead">A complete operating system for cleaning companies — not a booking widget.</p>
    <div class="grid">
        @foreach($modules as $module)
            <a class="item" href="{{ route('modules.show', $module['key']) }}">
                <h3>{{ $module['name'] }}</h3>
                <p>{{ $module['description'] }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
