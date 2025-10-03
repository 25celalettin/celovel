<?php

namespace App\Http\Controllers;

use Celovel\Http\Controller;
use Celovel\Http\Request;
use Celovel\Http\Response;

class HomeController extends Controller
{
    public function index(Request $request): Response
    {
        return $this->view('home', [
            'features' => [
                'Modern Routing Sistemi',
                'Controller Yapısı',
                'Service Container ve Dependency Injection',
                'Request/Response Yönetimi',
                'Blade Template Engine',
                'Database ORM (Eloquent benzeri)',
                'Configuration Management',
                'Middleware Sistemi (yakında)'
            ],
            'version' => '1.0.0',
            'time' => date('Y-m-d H:i:s'),
            'showNav' => true,
            'users' => [
                ['name' => 'Ahmet Yılmaz', 'email' => 'ahmet@example.com'],
                ['name' => 'Ayşe Demir', 'email' => 'ayse@example.com'],
                ['name' => 'Mehmet Kaya', 'email' => 'mehmet@example.com']
            ]
        ]);
    }

    public function about()
    {
        return [
            'name' => 'Celovel Framework',
            'description' => 'Laravel benzeri modern PHP framework',
            'features' => [
                'Routing',
                'Controllers',
                'Service Container',
                'Request/Response',
                'Blade Template Engine',
                'Database ORM',
                'Configuration Management'
            ]
        ];
    }

    public function test(Request $request)
    {
        $data = $request->input();
        
        return [
            'method' => $request->getMethod(),
            'path' => $request->getPath(),
            'data' => $data,
            'headers' => $request->header(),
            'is_ajax' => $request->isAjax()
        ];
    }

    public function users()
    {
        return response()->json([
            'users' => [
                ['id' => 1, 'name' => 'Ahmet Yılmaz', 'email' => 'ahmet@example.com'],
                ['id' => 2, 'name' => 'Ayşe Demir', 'email' => 'ayse@example.com'],
                ['id' => 3, 'name' => 'Mehmet Kaya', 'email' => 'mehmet@example.com']
            ],
            'total' => 3,
            'message' => 'Controller\'dan kullanıcılar getirildi'
        ], 200);
    }
}
