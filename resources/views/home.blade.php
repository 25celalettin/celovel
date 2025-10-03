@extends('layouts.app')

@section('title', 'Celovel Framework - Ana Sayfa')
@section('subtitle', 'Laravel benzeri modern PHP framework')

@section('content')
<h2>Hoş Geldiniz!</h2>
<p>Celovel Framework'e hoş geldiniz! Bu modern PHP framework'ü Laravel'e benzer özellikler sunar.</p>

<h3>Mevcut Özellikler:</h3>
<ul class="feature-list">
    @foreach($features as $feature)
        <li>{{ $feature }}</li>
    @endforeach
</ul>

<h3>Hızlı Başlangıç</h3>
<p>Framework'ü kullanmaya başlamak için:</p>
<ol>
    <li>Routes dosyasında yeni route'lar tanımlayın</li>
    <li>Controller'lar oluşturun</li>
    <li>Blade template dosyalarını yazın</li>
    <li>Geliştirme sunucusunu başlatın: <code>composer serve</code></li>
</ol>

<div class="info-box" style="background: #e3f2fd; padding: 15px; border-radius: 5px; margin: 20px 0;">
    <strong>Versiyon:</strong> {{ $version ?? '1.0.0' }}<br>
    <strong>Zaman:</strong> {{ $time ?? date('Y-m-d H:i:s') }}
</div>

@if(!empty($users))
<h3>Örnek Kullanıcılar</h3>
<div class="user-list">
    @foreach($users as $user)
        <div class="user-item" style="padding: 10px; border: 1px solid #ddd; margin: 5px 0; border-radius: 5px;">
            <strong>{{ $user['name'] }}</strong> - {{ $user['email'] }}
        </div>
    @endforeach
</div>
@endif

@endsection

@section('scripts')
<script>
    console.log('Celovel Framework yüklendi!');
</script>
@endsection
