@extends('layouts.website')
@section('title', $solution['title'].' · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>{{ $solution['title'] }}</h2>
    <p class="lead">{{ $solution['body'] }}</p>
    <a class="btn btn-primary" href="{{ route('trial') }}">Start Free Trial</a>
</section>
@endsection
