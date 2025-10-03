<?php

use App\Http\Controllers\HomeController;

// Ana sayfa
$app->get('/', [HomeController::class, 'index']);

// Hakkında sayfası
$app->get('/about', [HomeController::class, 'about']);

// Test sayfası
$app->get('/test', [HomeController::class, 'test']);
$app->post('/test', [HomeController::class, 'test']);

// Controller'dan response()->json() kullanımı
$app->get('/controller-users', [HomeController::class, 'users']);

// Blade test sayfası
$app->get('/blade-test', function($request) {
    return view('test', ['version' => '1.0.0']);
});

// Basit Blade test sayfası
$app->get('/simple-test', function($request) {
    return view('simple-test', ['version' => '1.0.0']);
});

// Basit closure route
$app->get('/hello', function($request) {
    return response('Merhaba Celovel!', 200);
});

// JSON response örneği - artık sadece array döndürmek yeterli!
$app->get('/api/status', function($request) {
    return [
        'status' => 'ok',
        'timestamp' => time(),
        'framework' => 'Celovel'
    ];
});

// Laravel benzeri response()->json() kullanımı
$app->get('/api/users', function($request) {
    return response()->json([
        'users' => [
            ['id' => 1, 'name' => 'Ahmet Yılmaz', 'email' => 'ahmet@example.com'],
            ['id' => 2, 'name' => 'Ayşe Demir', 'email' => 'ayse@example.com'],
            ['id' => 3, 'name' => 'Mehmet Kaya', 'email' => 'mehmet@example.com']
        ],
        'total' => 3,
        'message' => 'Kullanıcılar başarıyla getirildi'
    ], 200);
});

// Error response örneği
$app->get('/api/error', function($request) {
    return response()->json([
        'error' => 'Not Found',
        'message' => 'Aradığınız kaynak bulunamadı',
        'code' => 404
    ], 404);
});

// Success response örneği
$app->get('/api/success', function($request) {
    return response()->json([
        'success' => true,
        'message' => 'İşlem başarıyla tamamlandı',
        'data' => [
            'id' => 123,
            'created_at' => date('Y-m-d H:i:s')
        ]
    ], 201);
});

// Middleware'li route'lar
$app->get('/protected', function($request) {
    return [
        'message' => 'Bu route korumalı!',
        'user_id' => $request->getAttribute('user_id'),
        'token' => $request->getAttribute('user_token')
    ];
}, ['auth']);

$app->get('/api/protected', function($request) {
    return [
        'message' => 'API korumalı route',
        'user_id' => $request->getAttribute('user_id'),
        'timestamp' => time()
    ];
}, ['auth']);

$app->get('/cors-test', function($request) {
    return [
        'message' => 'CORS test route',
        'origin' => $request->header('Origin'),
        'method' => $request->getMethod()
    ];
}, ['cors']);

// Debug route - middleware'leri test et
$app->get('/debug-middleware', function($request) {
    return [
        'message' => 'Debug middleware test',
        'middleware_works' => true
    ];
}, ['auth']);
