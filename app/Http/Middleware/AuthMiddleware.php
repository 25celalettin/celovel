<?php

namespace App\Http\Middleware;

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class AuthMiddleware extends Middleware
{
    public function handle(Request $request, \Closure $next): Response
    {
        // Basit token kontrolü
        $token = $request->header('Authorization') ?? $request->header('authorization');
        
        
        if (!$token) {
            return $this->unauthorizedResponse('Token gerekli');
        }

        // Bearer token formatını kontrol et
        if (!str_starts_with($token, 'Bearer ')) {
            return $this->unauthorizedResponse('Geçersiz token formatı');
        }

        $token = substr($token, 7); // "Bearer " kısmını çıkar

        // Token'ı doğrula (basit örnek)
        if (!$this->validateToken($token)) {
            return $this->unauthorizedResponse('Geçersiz token');
        }

        // Token'ı request'e ekle
        $request->setAttribute('user_token', $token);
        $request->setAttribute('user_id', $this->getUserIdFromToken($token));

        return $next($request);
    }

    protected function validateToken(string $token): bool
    {
        // Basit token doğrulama (gerçek uygulamada JWT veya benzeri kullanılmalı)
        return strlen($token) >= 10 && ctype_alnum($token);
    }

    protected function getUserIdFromToken(string $token): int
    {
        // Token'dan user ID'yi çıkar (basit örnek)
        return crc32($token) % 1000;
    }

    protected function unauthorizedResponse(string $message): Response
    {
        return (new Response())->json([
            'error' => 'Unauthorized',
            'message' => $message
        ], 401);
    }
}
