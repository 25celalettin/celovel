<?php

namespace Celovel\Support;

class Env
{
    protected static array $variables = [];

    public static function load(?string $file = null): void
    {
        $file = $file ?: __DIR__ . '/../../.env';
        
        if (!file_exists($file)) {
            return;
        }

        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            if (strpos(trim($line), '#') === 0) {
                continue;
            }

            list($name, $value) = self::parseLine($line);
            
            if ($name !== null) {
                self::$variables[$name] = $value;
                putenv("{$name}={$value}");
            }
        }
    }

    protected static function parseLine(string $line): array
    {
        if (strpos($line, '=') === false) {
            return [null, null];
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);

        // Remove quotes
        if (preg_match('/^"(.*)"$/', $value, $matches)) {
            $value = $matches[1];
        } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
            $value = $matches[1];
        }

        return [$name, $value];
    }

    public static function get(string $key, $default = null)
    {
        $value = getenv($key);
        
        if ($value === false) {
            return $default;
        }

        // Convert string values to appropriate types
        if (strtolower($value) === 'true') {
            return true;
        }
        
        if (strtolower($value) === 'false') {
            return false;
        }
        
        if (strtolower($value) === 'null') {
            return null;
        }

        return $value;
    }

    public static function set(string $key, $value): void
    {
        self::$variables[$key] = $value;
        putenv("{$key}={$value}");
    }

    public static function has(string $key): bool
    {
        return getenv($key) !== false;
    }
}
