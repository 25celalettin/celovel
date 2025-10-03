<?php

namespace Celovel\Http;

class Response
{
    protected $content;
    protected int $statusCode;
    protected array $headers;

    public function __construct($content = '', int $statusCode = 200, array $headers = [])
    {
        $this->content = $content;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
    }

    public function send(): void
    {
        $this->sendHeaders();
        $this->sendContent();
    }

    protected function sendHeaders(): void
    {
        if (headers_sent()) {
            return;
        }

        http_response_code($this->statusCode);

        foreach ($this->headers as $name => $value) {
            header($name . ': ' . $value);
        }
    }

    protected function sendContent(): void
    {
        echo $this->content;
    }

    public function setContent($content): self
    {
        $this->content = $content;
        return $this;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setStatusCode(int $statusCode): self
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setHeader(string $name, string $value): self
    {
        $this->headers[$name] = $value;
        return $this;
    }

    public function getHeader(string $name): ?string
    {
        return $this->headers[$name] ?? null;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }


    public function json(array $data, int $statusCode = 200): self
    {
        $this->setContent(json_encode($data));
        $this->setStatusCode($statusCode);
        $this->setHeader('Content-Type', 'application/json');
        return $this;
    }

    public function redirect(string $url, int $statusCode = 302): self
    {
        $this->setStatusCode($statusCode);
        $this->setHeader('Location', $url);
        return $this;
    }

    public function view(string $view, array $data = []): self
    {
        // View engine implementasyonu burada olacak
        $this->setContent("View: {$view} with data: " . json_encode($data));
        return $this;
    }

    public static function make($content = '', int $statusCode = 200, array $headers = []): self
    {
        return new self($content, $statusCode, $headers);
    }


}
