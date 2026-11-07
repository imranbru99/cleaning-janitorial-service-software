@extends('layouts.portal')
@section('content')
<h1>Invoices</h1>
@foreach($invoices as $i)<div class="card"><strong>{{ $i->number }}</strong><p>{{ $i->status }} · Due {{ $i->due_date }} · Balance {{ $i->balance_due }}</p></div>@endforeach
{{ $invoices->links() }}
@endsection
