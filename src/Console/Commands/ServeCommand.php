<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class ServeCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $host = $this->getOption($options, 'host', 'localhost');
        $port = (int) $this->getOption($options, 'port', '8000');
        
        // Port kullanılabilir mi kontrol et
        $availablePort = $this->findAvailablePort($host, $port);
        
        if ($availablePort !== $port) {
            $this->warning("Port {$port} is already in use. Using port {$availablePort} instead.");
        }
        
        $this->info("Celovel development server started:");
        $this->line("  <info>Local:</info>   http://{$host}:{$availablePort}");
        $this->line("  <info>Network:</info> http://0.0.0.0:{$availablePort}");
        $this->line('');
        $this->info("Press Ctrl+C to stop the server");
        $this->line('');

        // PHP built-in server'ı başlat
        $command = "php -S {$host}:{$availablePort} -t public";
        passthru($command);
    }

    protected function findAvailablePort(string $host, int $startPort): int
    {
        $port = $startPort;
        $maxAttempts = 10; // Maksimum 10 port dene
        
        for ($i = 0; $i < $maxAttempts; $i++) {
            if ($this->isPortAvailable($host, $port)) {
                return $port;
            }
            $port++;
        }
        
        // Eğer hiç port bulunamazsa başlangıç portunu döndür
        return $startPort;
    }

    protected function isPortAvailable(string $host, int $port): bool
    {
        $connection = @fsockopen($host, $port, $errno, $errstr, 1);
        
        if ($connection) {
            fclose($connection);
            return false; // Port kullanımda
        }
        
        return true; // Port kullanılabilir
    }

    public function getDescription(): string
    {
        return 'Start the development server';
    }
}
