@extends('layouts.website')
@section('title', 'Blog')
@section('content')
<section class="wrap section">
    <h2>Blog</h2>
    <div class="grid">
        @foreach($posts as $post)
            <a class="item" href="{{ route('blog.show', $post->slug) }}"><h3>{{ $post->title }}</h3><p>{{ $post->excerpt }}</p></a>
        @endforeach
    </div>
    <div style="margin-top:1.5rem">{{ $posts->links() }}</div>
</section>
@endsection
