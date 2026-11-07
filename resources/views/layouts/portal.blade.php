<!DOCTYPE html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Customer Portal</title>
<link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700&family=Sora:wght@600&display=swap" rel="stylesheet">
<style>
body{margin:0;font-family:Manrope,sans-serif;background:#F1F5F9;color:#0F172A}
.wrap{width:min(960px,calc(100% - 2rem));margin:2rem auto}
.card{background:white;border-radius:1rem;padding:1.25rem;margin-bottom:1rem;border:1px solid rgba(15,23,42,.08)}
h1,h2{font-family:Sora,sans-serif}
nav a{margin-right:1rem;color:#0B1F3A;text-decoration:none;font-weight:600}
</style></head><body>
<div class="wrap">
<nav>
<a href="{{ route('portal.dashboard') }}">Dashboard</a>
<a href="{{ route('portal.bookings') }}">Bookings</a>
<a href="{{ route('portal.invoices') }}">Invoices</a>
<a href="{{ route('portal.inspections') }}">Inspections</a>
</nav>
@yield('content')
</div></body></html>
