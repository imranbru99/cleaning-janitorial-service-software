@extends('layouts.portal')
@section('content')
<h1>Bookings</h1>
@foreach($bookings as $b)<div class="card"><strong>{{ $b->reference }}</strong><p>{{ $b->status }} · {{ $b->scheduled_start }} · {{ $b->total }}</p></div>@endforeach
{{ $bookings->links() }}
@endsection
