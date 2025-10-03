@extends('layouts.simple')

@section('title', 'Test Sayfası')

@section('content')
<h2>Test Sayfası</h2>
<p>Bu bir test sayfasıdır.</p>
<p>Versiyon: {{ $version ?? '1.0.0' }}</p>
@endsection
