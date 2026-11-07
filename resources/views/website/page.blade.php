@extends('layouts.website')
@section('title', $title.' · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>{{ $title }}</h2>
    <p class="lead">{{ $subtitle ?? '' }}</p>
    <div class="item"><p>{!! nl2br(e($content)) !!}</p></div>
</section>
@endsection
