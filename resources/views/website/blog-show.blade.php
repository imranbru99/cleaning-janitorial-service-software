@extends('layouts.website')
@section('title', $post->title)
@section('content')
<section class="wrap section">
    <h2>{{ $post->title }}</h2>
    <p class="lead">{{ $post->excerpt }}</p>
    <div class="item"><p>{!! nl2br(e($post->content)) !!}</p></div>
</section>
@endsection
