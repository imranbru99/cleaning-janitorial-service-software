@extends('layouts.website')
@section('title', 'Contact')
@section('content')
<section class="wrap section">
    <h2>Contact</h2>
    <form class="form" method="post" action="{{ route('contact.submit') }}">
        @csrf
        <input name="name" placeholder="Name" required>
        <input name="email" type="email" placeholder="Email" required>
        <textarea name="message" rows="6" placeholder="How can we help?" required></textarea>
        <button class="btn btn-primary" type="submit">Send Message</button>
    </form>
</section>
@endsection
