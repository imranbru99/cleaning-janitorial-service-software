@extends('layouts.website')
@section('title', 'FAQ')
@section('content')
<section class="wrap section">
    <h2>Frequently asked questions</h2>
    <div class="grid">
        @forelse($faqs as $faq)
            <div class="item"><h3>{{ $faq->question }}</h3><p>{{ $faq->answer }}</p></div>
        @empty
            <div class="item"><h3>Can I disable modules?</h3><p>Yes. Disabled modules leave navigation and routes, while historical records remain intact.</p></div>
        @endforelse
    </div>
</section>
@endsection
