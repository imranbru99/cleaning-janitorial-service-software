@extends('layouts.website')
@section('title', $title)
@section('content')
<section class="wrap section">
    <h2>{{ $title }}</h2>
    <div class="item"><p>This {{ strtolower($title) }} applies to the CleanOps platform. Replace with counsel-approved legal language before production launch. Contact privacy@cleaningdemo.test for data requests.</p></div>
</section>
@endsection
