<?php

namespace Celovel\Console;

use Celovel\Console\Commands\ServeCommand;
use Celovel\Console\Commands\MakeControllerCommand;
use Celovel\Console\Commands\MakeModelCommand;
use Celovel\Console\Commands\MakeMiddlewareCommand;
use Celovel\Console\Commands\RouteListCommand;
use Celovel\Console\Commands\CacheClearCommand;

class Application
{
    protected array $commands = [];
    protected array $arguments = [];
    protected array $options = [];

    public function __construct()
    {
        $this->registerCommands();
    }

    protected function registerCommands(): void
    {
        $this->commands = [
            'serve' => new ServeCommand(),
            'make:controller' => new MakeControllerCommand(),
            'make:model' => new MakeModelCommand(),
            'make:middleware' => new MakeMiddlewareCommand(),
            'route:list' => new RouteListCommand(),
            'cache:clear' => new CacheClearCommand(),
        ];
    }

    public function run(): void
    {
        $this->parseArguments();
        
        if (empty($this->arguments)) {
            $this->showHelp();
            return;
        }

        $command = $this->arguments[0];
        
        if (!isset($this->commands[$command])) {
            $this->error("Command '{$command}' not found.");
            $this->showAvailableCommands();
            return;
        }

        $this->commands[$command]->execute(array_slice($this->arguments, 1), $this->options);
    }

    protected function parseArguments(): void
    {
        global $argv;
        
        if (count($argv) < 2) {
            return;
        }

        $this->arguments = array_slice($argv, 1);
        
        // Parse options
        foreach ($this->arguments as $arg) {
            if (str_starts_with($arg, '--')) {
                $option = substr($arg, 2);
                if (str_contains($option, '=')) {
                    [$key, $value] = explode('=', $option, 2);
                    $this->options[$key] = $value;
                } else {
                    $this->options[$option] = true;
                }
            }
        }
    }

    protected function showHelp(): void
    {
        $this->info('Celovel Framework Console');
        $this->line('');
        $this->info('Usage:');
        $this->line('  php celovel <command> [options]');
        $this->line('');
        $this->showAvailableCommands();
    }

    protected function showAvailableCommands(): void
    {
        $this->info('Available commands:');
        $this->line('');
        
        foreach ($this->commands as $name => $command) {
            $this->line("  <info>{$name}</info> - {$command->getDescription()}");
        }
    }

    public function info(string $message): void
    {
        echo "\033[32m{$message}\033[0m\n";
    }

    public function error(string $message): void
    {
        echo "\033[31m{$message}\033[0m\n";
    }

    public function warning(string $message): void
    {
        echo "\033[33m{$message}\033[0m\n";
    }

    public function line(string $message = ''): void
    {
        echo "{$message}\n";
    }
}
