@extends('layouts.website')
@section('title', 'Modules · '.config('cleaning.short_name'))
@section('content')
<section class="wrap section">
    <h2>Module directory</h2>
    <p class="lead">Enable capabilities per subscription and operational need. Disabling a module hides navigation and routes without deleting history.</p>
    <div class="grid">
        @foreach($modules as $module)
            <a class="item" href="{{ route('modules.show', $module['key']) }}">
                <h3>{{ $module['name'] }}</h3>
                <p>{{ $module['description'] }}</p>
            </a>
        @endforeach
    </div>
</section>
@endsection
