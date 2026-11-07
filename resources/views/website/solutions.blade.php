@extends('layouts.website')
@section('title', 'Solutions · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>Industry solutions</h2>
    <p class="lead">Purpose-built workflows for the cleaning markets you serve.</p>
    <div class="grid">
        @foreach(['residential'=>'Residential','commercial'=>'Commercial','janitorial'=>'Janitorial','healthcare'=>'Healthcare','hotel'=>'Hotel','school'=>'School','industrial'=>'Industrial','franchise'=>'Franchise'] as $slug=>$label)
            <a class="item" href="{{ route('solutions.show', $slug) }}"><h3>{{ $label }} Cleaning</h3><p>Explore operational playbooks for {{ strtolower($label) }} teams.</p></a>
        @endforeach
    </div>
</section>
@endsection
