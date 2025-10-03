# 🚀 Celovel Framework

Laravel benzeri modern PHP web framework'ü. MVC mimarisi, routing, middleware, service container ve daha birçok özellik ile güçlü web uygulamaları geliştirin.

## ✨ Özellikler

### 🏗️ Temel Mimari
- **MVC Pattern** - Model, View, Controller ayrımı
- **Service Container** - Dependency Injection sistemi
- **PSR-4 Autoloading** - Modern PHP standartları
- **Environment Configuration** - .env dosyası desteği

### 🌐 HTTP Katmanı
- **RESTful Routing** - GET, POST, PUT, DELETE desteği
- **Request/Response** - HTTP istekleri ve yanıtları
- **Middleware System** - Request pipeline yönetimi
- **Automatic JSON Response** - Array döndürme otomatik JSON'a çevrilir

### 🎨 View Sistemi
- **Blade-like Template Engine** - Laravel benzeri template sistemi
- **Layout Inheritance** - @extends, @section, @yield
- **Template Caching** - Performans optimizasyonu
- **Helper Functions** - view(), url(), asset() fonksiyonları

### 🗄️ Veritabanı
- **Eloquent-like ORM** - Model tabanlı veritabanı işlemleri
- **Query Builder** - Fluent API ile sorgu oluşturma
- **Database Connection** - Çoklu veritabanı desteği
- **Migration Ready** - Veritabanı şema yönetimi

### 🛠️ CLI Tool
- **Artisan-like Commands** - Laravel benzeri komut satırı arayüzü
- **Code Generation** - Controller, Model, Middleware oluşturma
- **Development Server** - Otomatik port bulma ile server başlatma
- **Route Listing** - Kayıtlı route'ları listeleme

## 📦 Kurulum

### Gereksinimler
- PHP 8.1+
- Composer
- Web Server (Apache/Nginx) veya PHP Built-in Server

### Adım 1: Projeyi İndirin
```bash
git clone https://github.com/your-username/celovel.git
cd celovel
```

### Adım 2: Bağımlılıkları Yükleyin
```bash
composer install
```

### Adım 3: Environment Dosyasını Oluşturun
```bash
cp .env.example .env
```

### Adım 4: Development Server'ı Başlatın
```bash
php celovel serve
```

Server `http://localhost:8000` adresinde çalışmaya başlayacak.

## 🚀 Hızlı Başlangıç

### 1. İlk Route'unuzu Oluşturun

`routes/web.php` dosyasını açın ve route'larınızı tanımlayın:

```php
<?php

use App\Http\Controllers\HomeController;

// Basit route
$app->get('/', function($request) {
    return [
        'message' => 'Celovel Framework\'e hoş geldiniz!',
        'version' => '1.0.0'
    ];
});

// Controller route
$app->get('/home', [HomeController::class, 'index']);

// Middleware'li route
$app->get('/protected', function($request) {
    return ['message' => 'Bu route korumalı!'];
}, ['auth']);
```

### 2. Controller Oluşturun

```bash
php celovel make:controller HomeController
```

Oluşturulan controller'ı düzenleyin:

```php
<?php

namespace App\Http\Controllers;

use Celovel\Http\Controller;
use Celovel\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        return [
            'message' => 'Ana sayfa',
            'method' => $request->getMethod(),
            'path' => $request->getPath()
        ];
    }
}
```

### 3. Model Oluşturun

```bash
php celovel make:model User
```

```php
<?php

namespace App\Models;

use Celovel\Database\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email'];
    protected $guarded = ['id'];
}
```

## 🛣️ Routing

### Temel Route'lar

```php
// GET route
$app->get('/users', function($request) {
    return ['users' => UserModel::all()];
});

// POST route
$app->post('/users', function($request) {
    $data = $request->input();
    return UserModel::create($data);
});

// PUT route
$app->put('/users/{id}', function($request) {
    $id = $request->get('id');
    $data = $request->input();
    return UserModel::find($id)->update($data);
});

// DELETE route
$app->delete('/users/{id}', function($request) {
    $id = $request->get('id');
    return UserModel::find($id)->delete();
});
```

### Controller Route'ları

```php
// Array syntax
$app->get('/users', [UserController::class, 'index']);

// String syntax
$app->get('/users', 'UserController@index');
```

### Middleware'li Route'lar

```php
// Tek middleware
$app->get('/admin', [AdminController::class, 'index'], ['auth']);

// Çoklu middleware
$app->get('/admin/users', [AdminController::class, 'users'], ['auth', 'admin']);

// Global middleware (tüm route'larda çalışır)
// Application.php'de tanımlanır
```

## 🎨 View Sistemi

### Blade Template Engine

Celovel, Laravel'in Blade template engine'ine benzer güçlü bir template sistemi sunar.

#### Layout Dosyası (`resources/views/layouts/app.blade.php`)

```html
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Celovel Framework')</title>
</head>
<body>
    @if($showNav ?? false)
    <nav>
        <a href="{{ url('/') }}">Ana Sayfa</a>
        <a href="{{ url('/about') }}">Hakkında</a>
    </nav>
    @endif

    <main>
        @yield('content')
    </main>
</body>
</html>
```

#### View Dosyası (`resources/views/home.blade.php`)

```html
@extends('layouts.app')

@section('title', 'Ana Sayfa')

@section('content')
<h1>Hoş Geldiniz!</h1>

@if(!empty($users))
    <h3>Kullanıcılar</h3>
    <ul>
        @foreach($users as $user)
            <li>{{ $user['name'] }} - {{ $user['email'] }}</li>
        @endforeach
    </ul>
@endif
@endsection
```

#### Controller'da View Kullanımı

```php
public function index(Request $request)
{
    $users = UserModel::all();
    
    return $this->view('home', [
        'users' => $users,
        'showNav' => true
    ]);
}
```

### Desteklenen Blade Direktifleri

- `@extends('layout')` - Layout kalıtımı
- `@section('name')` / `@endsection` - Section tanımlama
- `@yield('name', 'default')` - Section render etme
- `@if` / `@elseif` / `@else` / `@endif` - Koşullu ifadeler
- `@foreach` / `@endforeach` - Döngüler
- `@for` / `@endfor` - For döngüleri
- `@while` / `@endwhile` - While döngüleri
- `@unless` / `@endunless` - Unless koşulları
- `@isset` / `@endisset` - Değişken kontrolü
- `@empty` / `@endempty` - Boş kontrolü
- `@include('view')` - View dahil etme
- `@php` / `@endphp` - PHP kodu
- `{{ $variable }}` - Escape edilmiş çıktı
- `{!! $html !!}` - Raw HTML çıktı

## 🛡️ Middleware Sistemi

### Middleware Oluşturma

```bash
php celovel make:middleware AuthMiddleware
```

```php
<?php

namespace App\Http\Middleware;

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        $token = $request->header('Authorization');
        
        if (!$token) {
            return $this->unauthorizedResponse('Token gerekli');
        }

        // Token doğrulama logic'i
        if (!$this->validateToken($token)) {
            return $this->unauthorizedResponse('Geçersiz token');
        }

        return $next($request);
    }

    protected function validateToken(string $token): bool
    {
        // Token doğrulama logic'i
        return strlen($token) >= 10;
    }

    protected function unauthorizedResponse(string $message): Response
    {
        return (new Response())->json([
            'error' => 'Unauthorized',
            'message' => $message
        ], 401);
    }
}
```

### Middleware Kaydetme

`src/Core/Application.php` dosyasında:

```php
protected function registerMiddleware(): void
{
    // Global middleware'ler
    $this->router->registerGlobalMiddleware(\App\Http\Middleware\LoggingMiddleware::class);
    
    // Named middleware'ler
    $this->router->registerMiddleware('auth', \App\Http\Middleware\AuthMiddleware::class);
    $this->router->registerMiddleware('cors', \App\Http\Middleware\CorsMiddleware::class);
}
```

### Middleware Kullanımı

```php
// Route'da middleware kullanma
$app->get('/protected', function($request) {
    return ['message' => 'Korumalı route'];
}, ['auth']);

// Çoklu middleware
$app->get('/admin', [AdminController::class, 'index'], ['auth', 'admin']);
```

## 🗄️ Veritabanı ve ORM

### Model Kullanımı

```php
<?php

namespace App\Models;

use Celovel\Database\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $fillable = ['name', 'email', 'password'];
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
```

### Veritabanı İşlemleri

```php
// Tüm kayıtları getir
$users = UserModel::all();

// ID ile bul
$user = UserModel::find(1);

// Koşullu sorgu
$users = UserModel::where('active', 1)->get();

// Yeni kayıt oluştur
$user = UserModel::create([
    'name' => 'Ahmet Yılmaz',
    'email' => 'ahmet@example.com'
]);

// Kayıt güncelle
$user = UserModel::find(1);
$user->update(['name' => 'Yeni İsim']);

// Kayıt sil
UserModel::find(1)->delete();
```

### Query Builder

```php
use Celovel\Database\QueryBuilder;

$query = new QueryBuilder();
$users = $query->table('users')
    ->where('active', 1)
    ->where('age', '>', 18)
    ->orderBy('name', 'ASC')
    ->limit(10)
    ->get();
```

## 🛠️ CLI Komutları

### Mevcut Komutlar

```bash
# Development server başlat
php celovel serve
php celovel serve --port=8000
php celovel serve --host=0.0.0.0 --port=8000

# Controller oluştur
php celovel make:controller UserController

# Model oluştur
php celovel make:model Product

# Middleware oluştur
php celovel make:middleware AdminAuth

# Route'ları listele
php celovel route:list

# Cache'i temizle
php celovel cache:clear

# Yardım
php celovel
php celovel --help
```

### Otomatik Port Bulma

Serve komutu port çakışması durumunda otomatik olarak uygun port bulur:

```bash
php celovel serve
# Port 8000 kullanımda ise otomatik olarak 8001, 8002, vb. dener
```

## 🔧 Konfigürasyon

### Environment Variables

`.env` dosyasında:

```env
APP_NAME=Celovel
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=celovel
DB_USERNAME=root
DB_PASSWORD=
```

### Service Container

```php
// Service kaydetme
$container->singleton('logger', function() {
    return new Logger();
});

// Service kullanma
$logger = $container->make('logger');
```

## 📁 Proje Yapısı

```
celovel/
├── app/                          # Uygulama dosyaları
│   ├── Http/
│   │   ├── Controllers/         # Controller'lar
│   │   └── Middleware/          # Middleware'ler
│   └── Models/                  # Model'ler
├── config/                      # Konfigürasyon dosyaları
├── public/                      # Web root
│   └── index.php               # Entry point
├── resources/
│   └── views/                  # View dosyaları
│       ├── layouts/            # Layout dosyaları
│       └── *.blade.php         # Blade template'ler
├── routes/
│   └── web.php                 # Web route'ları
├── src/                        # Framework core
│   ├── Console/                # CLI komutları
│   ├── Core/                   # Core sınıflar
│   ├── Database/               # ORM ve veritabanı
│   ├── Http/                   # HTTP katmanı
│   ├── Support/                # Yardımcı sınıflar
│   └── View/                   # View sistemi
├── storage/
│   └── framework/
│       └── views/              # Compiled view cache
├── celovel                     # CLI executable
├── composer.json
└── README.md
```

## 🚀 Performans

### View Caching

Blade template'ler otomatik olarak cache'lenir ve sadece değişiklik olduğunda yeniden derlenir.

### Service Container

Singleton pattern ile aynı servisler tekrar oluşturulmaz, memory ve performans optimizasyonu sağlanır.

### Middleware Pipeline

Middleware'ler pipeline pattern ile optimize edilmiş şekilde çalışır.

## 🔒 Güvenlik

### Middleware Tabanlı Güvenlik

- Authentication middleware
- CORS middleware
- Rate limiting middleware
- Input validation

### Request Validation

```php
// Request'ten gelen verileri doğrula
$data = $request->input();
if (empty($data['email'])) {
    return (new Response())->json(['error' => 'Email gerekli'], 400);
}
```

## 🧪 Test

### Route Test

```bash
# Route'ları listele
php celovel route:list

# Server'ı test et
curl http://localhost:8000
```

### API Test

```bash
# JSON response test
curl http://localhost:8000/api/status

# Middleware test
curl -H "Authorization: Bearer test123456" http://localhost:8000/protected
```

## 📚 Örnekler

### Basit API Endpoint

```php
$app->get('/api/users', function($request) {
    return [
        'users' => UserModel::all(),
        'total' => UserModel::count(),
        'timestamp' => time()
    ];
});
```

### Form İşleme

```php
$app->post('/contact', function($request) {
    $data = $request->input();
    
    // Validation
    if (empty($data['name']) || empty($data['email'])) {
        return (new Response())->json(['error' => 'Gerekli alanlar eksik'], 400);
    }
    
    // İşleme
    // ...
    
    return ['message' => 'Mesajınız gönderildi'];
});
```

### Middleware'li Admin Panel

```php
$app->get('/admin', [AdminController::class, 'index'], ['auth', 'admin']);
$app->get('/admin/users', [AdminController::class, 'users'], ['auth', 'admin']);
$app->post('/admin/users', [AdminController::class, 'store'], ['auth', 'admin']);
```

## 🤝 Katkıda Bulunma

1. Fork yapın
2. Feature branch oluşturun (`git checkout -b feature/amazing-feature`)
3. Commit yapın (`git commit -m 'Add amazing feature'`)
4. Push yapın (`git push origin feature/amazing-feature`)
5. Pull Request oluşturun

## 📄 Lisans

Bu proje MIT lisansı altında lisanslanmıştır. Detaylar için `LICENSE` dosyasına bakın.

## 🙏 Teşekkürler

- Laravel framework'üne ilham verdiği için
- PHP topluluğuna katkıları için
- Tüm geliştiricilere

## 📞 İletişim

- GitHub: [@your-username](https://github.com/your-username)
- Email: your-email@example.com
- Website: https://celovel.dev

---

**Celovel Framework** - Modern PHP web development'ın geleceği! 🚀