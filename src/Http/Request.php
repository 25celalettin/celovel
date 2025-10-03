<?php

namespace Celovel\Http;

class Request
{
    protected array $query;
    protected array $request;
    protected array $server;
    protected array $files;
    protected array $cookies;
    protected array $headers;
    protected array $attributes = [];

    public function __construct()
    {
        $this->query = $_GET;
        $this->request = $_POST;
        $this->server = $_SERVER;
        $this->files = $_FILES;
        $this->cookies = $_COOKIE;
        $this->headers = $this->getHeaders();
    }

    public static function createFromGlobals(): self
    {
        return new self();
    }

    public function getMethod(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function getPath(): string
    {
        $path = $this->server['REQUEST_URI'] ?? '/';
        $path = parse_url($path, PHP_URL_PATH);
        return $path ?: '/';
    }

    public function get(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->query;
        }
        return $this->query[$key] ?? $default;
    }

    public function post(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->request;
        }
        return $this->request[$key] ?? $default;
    }

    public function input(?string $key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($this->query, $this->request);
        }
        return $this->get($key) ?? $this->post($key) ?? $default;
    }

    public function file(?string $key = null)
    {
        if ($key === null) {
            return $this->files;
        }
        return $this->files[$key] ?? null;
    }

    public function header(?string $key = null)
    {
        if ($key === null) {
            return $this->headers;
        }
        return $this->headers[$key] ?? null;
    }

    public function cookie(?string $key = null, $default = null)
    {
        if ($key === null) {
            return $this->cookies;
        }
        return $this->cookies[$key] ?? $default;
    }

    public function isMethod(string $method): bool
    {
        return $this->getMethod() === strtoupper($method);
    }

    public function isAjax(): bool
    {
        return $this->header('X-Requested-With') === 'XMLHttpRequest';
    }

    public function isJson(): bool
    {
        return str_contains($this->header('Content-Type') ?? '', 'application/json');
    }

    public function getJson(): array
    {
        if (!$this->isJson()) {
            return [];
        }

        $json = file_get_contents('php://input');
        return json_decode($json, true) ?? [];
    }

    protected function getHeaders(): array
    {
        $headers = [];
        
        if (function_exists('getallheaders')) {
            $headers = getallheaders();
        } else {
            foreach ($_SERVER as $key => $value) {
                if (str_starts_with($key, 'HTTP_')) {
                    $header = str_replace('_', '-', substr($key, 5));
                    $headers[$header] = $value;
                }
            }
        }

        return array_change_key_case($headers, CASE_LOWER);
    }

    public function getUrl(): string
    {
        $scheme = $this->server['HTTPS'] ?? 'off';
        $scheme = $scheme === 'on' ? 'https' : 'http';
        $host = $this->server['HTTP_HOST'] ?? 'localhost';
        $path = $this->getPath();
        
        return $scheme . '://' . $host . $path;
    }

    public function getFullUrl(): string
    {
        return $this->server['REQUEST_URI'] ?? '/';
    }

    public function setAttribute(string $key, $value): void
    {
        $this->attributes[$key] = $value;
    }

    public function getAttribute(string $key, $default = null)
    {
        return $this->attributes[$key] ?? $default;
    }

    public function getAttributes(): array
    {
        return $this->attributes;
    }
}
