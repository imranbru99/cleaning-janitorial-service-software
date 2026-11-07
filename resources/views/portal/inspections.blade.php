@extends('layouts.portal')
@section('content')
<h1>Inspection summaries</h1>
@foreach($inspections as $ins)<div class="card"><strong>#{{ $ins->id }}</strong><p>{{ $ins->result }} · Score {{ $ins->score }}</p></div>@endforeach
{{ $inspections->links() }}
@endsection
