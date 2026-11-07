@extends('layouts.website')
@section('title', 'Request a Demo')
@section('content')
<section class="wrap section">
    <h2>Book a demo</h2>
    <p class="lead">See scheduling, inspections, and billing workflows with realistic cleaning data.</p>
    <form class="form" method="post" action="{{ route('demo.submit') }}">
        @csrf
        <input name="name" placeholder="Your name" required>
        <input name="email" type="email" placeholder="Work email" required>
        <input name="company" placeholder="Company">
        <textarea name="message" rows="5" placeholder="What do you want to see?"></textarea>
        <button class="btn btn-primary" type="submit">Request Demo</button>
    </form>
</section>
@endsection
