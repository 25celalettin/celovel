<?php

use Celovel\Core\Application;

if (!function_exists('app')) {
    function app(?string $service = null)
    {
        static $app = null;
        
        if ($app === null) {
            $app = new Application();
        }
        
        if ($service === null) {
            return $app;
        }
        
        return $app->getContainer()->make($service);
    }
}

if (!function_exists('view')) {
    function view(string $view, array $data = [])
    {
        $content = app('view')->render($view, $data);
        return new \Celovel\Http\Response($content);
    }
}

if (!function_exists('config')) {
    function config(?string $key = null, $default = null)
    {
        $config = app('config');
        
        if ($key === null) {
            return $config;
        }
        
        return $config->get($key, $default);
    }
}

if (!function_exists('env')) {
    function env(string $key, $default = null)
    {
        return \Celovel\Support\Env::get($key, $default);
    }
}

if (!function_exists('route')) {
    function route(string $name, array $parameters = [])
    {
        // Basit route helper - daha sonra route name sistemi ekleyeceğiz
        return $name;
    }
}

if (!function_exists('url')) {
    function url(string $path = '')
    {
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        return $protocol . '://' . $host . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path)
    {
        return url('public/' . ltrim($path, '/'));
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token()
    {
        if (!isset($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('csrf_field')) {
    function csrf_field()
    {
        return '<input type="hidden" name="_token" value="' . csrf_token() . '">';
    }
}

if (!function_exists('dd')) {
    function dd(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
        die();
    }
}

if (!function_exists('dump')) {
    function dump(...$vars)
    {
        foreach ($vars as $var) {
            var_dump($var);
        }
    }
}

if (!function_exists('response')) {
    function response($content = '', int $statusCode = 200, array $headers = [])
    {
        return new \Celovel\Http\Response($content, $statusCode, $headers);
    }
}
