@extends('layouts.website')
@section('title', 'Free Trial')
@section('content')
<section class="wrap section">
    <h2>Start your free trial</h2>
    <p class="lead">Create an isolated business workspace with demo-ready modules.</p>
    <form class="form" method="post" action="{{ route('register.tenant') }}">
        @csrf
        <input name="business_name" placeholder="Business name" required value="{{ old('business_name') }}">
        <input name="name" placeholder="Your name" required value="{{ old('name') }}">
        <input name="email" type="email" placeholder="Email" required value="{{ old('email') }}">
        <input name="password" type="password" placeholder="Password" required>
        <input name="password_confirmation" type="password" placeholder="Confirm password" required>
        <select name="plan">
            <option value="starter">Starter</option>
            <option value="professional" selected>Professional</option>
            <option value="business">Business</option>
            <option value="enterprise">Enterprise</option>
        </select>
        @if($errors->any())
            <div class="alert">{{ $errors->first() }}</div>
        @endif
        <button class="btn btn-primary" type="submit">Create Workspace</button>
    </form>
</section>
@endsection
