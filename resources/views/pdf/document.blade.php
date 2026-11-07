<!DOCTYPE html><html><head><meta charset="utf-8"><style>body{font-family:DejaVu Sans,sans-serif;font-size:12px}h1{font-size:18px}</style></head><body>
<h1>{{ $title ?? 'Document' }}</h1>
@isset($invoice)
<p>Invoice {{ $invoice->number }} for {{ $invoice->customer->name ?? '' }}</p>
<p>Total: {{ $invoice->total }} {{ $invoice->currency }}</p>
<ul>@foreach($invoice->items as $item)<li>{{ $item->description }} — {{ $item->line_total }}</li>@endforeach</ul>
@endisset
@isset($inspection)
<p>Inspection #{{ $inspection->id }} · Score {{ $inspection->score }} · {{ $inspection->result }}</p>
<p>{{ $inspection->summary }}</p>
@endisset
</body></html>
