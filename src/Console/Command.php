<?php

namespace Celovel\Console;

abstract class Command
{
    public function __construct()
    {
        // Application instance'ı sadece gerektiğinde oluştur
    }

    abstract public function execute(array $arguments, array $options): void;
    abstract public function getDescription(): string;

    protected function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m\n";
    }

    protected function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }

    protected function warning(string $message): void
    {
        echo "\033[33m{$message}\033[0m\n";
    }

    protected function line(string $message = ''): void
    {
        echo "{$message}\n";
    }

    protected function getArgument(array $arguments, int $index, $default = null)
    {
        return $arguments[$index] ?? $default;
    }

    protected function getOption(array $options, string $key, $default = null)
    {
        return $options[$key] ?? $default;
    }

    protected function hasOption(array $options, string $key): bool
    {
        return isset($options[$key]);
    }
}
