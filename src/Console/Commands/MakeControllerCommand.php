<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class MakeControllerCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $name = $this->getArgument($arguments, 0);
        
        if (!$name) {
            $this->error('Controller name is required.');
            $this->line('Usage: php celovel make:controller <name>');
            return;
        }

        $name = $this->formatName($name);
        $filename = "app/Http/Controllers/{$name}.php";
        
        if (file_exists($filename)) {
            $this->error("Controller {$name} already exists!");
            return;
        }

        $this->createController($name, $filename);
        $this->info("Controller {$name} created successfully!");
    }

    protected function formatName(string $name): string
    {
        // Controller suffix ekle
        if (!str_ends_with($name, 'Controller')) {
            $name .= 'Controller';
        }
        
        return $name;
    }

    protected function createController(string $name, string $filename): void
    {
        $namespace = 'App\\Http\\Controllers';
        $class = $name;
        
        $content = "<?php

namespace {$namespace};

use Celovel\Http\Controller;
use Celovel\Http\Request;

class {$class} extends Controller
{
    public function index(Request \$request)
    {
        return [
            'message' => 'Hello from {$class}!',
            'method' => \$request->getMethod(),
            'path' => \$request->getPath()
        ];
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
        return 'Create a new controller class';
    }
}
