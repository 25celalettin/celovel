<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class MakeModelCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $name = $this->getArgument($arguments, 0);
        
        if (!$name) {
            $this->error('Model name is required.');
            $this->line('Usage: php celovel make:model <name>');
            return;
        }

        $name = $this->formatName($name);
        $filename = "app/Models/{$name}.php";
        
        if (file_exists($filename)) {
            $this->error("Model {$name} already exists!");
            return;
        }

        $this->createModel($name, $filename);
        $this->info("Model {$name} created successfully!");
    }

    protected function formatName(string $name): string
    {
        // Model suffix ekle
        if (!str_ends_with($name, 'Model')) {
            $name .= 'Model';
        }
        
        return $name;
    }

    protected function createModel(string $name, string $filename): void
    {
        $namespace = 'App\\Models';
        $class = $name;
        $table = strtolower(preg_replace('/([a-z])([A-Z])/', '$1_$2', $name));
        $table = str_replace('_model', '', $table);
        
        $content = "<?php

namespace {$namespace};

use Celovel\Database\Model;

class {$class} extends Model
{
    protected \$table = '{$table}';
    protected \$fillable = [];
    protected \$guarded = ['id'];
    
    // Model methods here
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
        return 'Create a new model class';
    }
}
