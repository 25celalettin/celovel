<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class MakeMiddlewareCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $name = $this->getArgument($arguments, 0);
        
        if (!$name) {
            $this->error('Middleware name is required.');
            $this->line('Usage: php celovel make:middleware <name>');
            return;
        }

        $name = $this->formatName($name);
        $filename = "app/Http/Middleware/{$name}.php";
        
        if (file_exists($filename)) {
            $this->error("Middleware {$name} already exists!");
            return;
        }

        $this->createMiddleware($name, $filename);
        $this->info("Middleware {$name} created successfully!");
    }

    protected function formatName(string $name): string
    {
        // Middleware suffix ekle
        if (!str_ends_with($name, 'Middleware')) {
            $name .= 'Middleware';
        }
        
        return $name;
    }

    protected function createMiddleware(string $name, string $filename): void
    {
        $namespace = 'App\\Http\\Middleware';
        $class = $name;
        
        $content = "<?php

namespace {$namespace};

use Celovel\Http\Middleware\Middleware;
use Celovel\Http\Request;
use Celovel\Http\Response;

class {$class} extends Middleware
{
    public function handle(Request \$request, \Closure \$next): Response
    {
        // Middleware logic here
        
        return \$next(\$request);
    }
}
";

        // Dizin yoksa oluştur
        $dir = dirname($filename);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        file_put_contents($filename, $content);
    }

    public function getDescription(): string
    {
        return 'Create a new middleware class';
    }
}
