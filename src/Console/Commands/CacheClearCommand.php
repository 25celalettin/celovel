<?php

namespace Celovel\Console\Commands;

use Celovel\Console\Command;

class CacheClearCommand extends Command
{
    public function execute(array $arguments, array $options): void
    {
        $cacheDir = 'storage/framework/views';
        
        if (!is_dir($cacheDir)) {
            $this->warning('Cache directory does not exist.');
            return;
        }

        $files = glob($cacheDir . '/*');
        $deleted = 0;
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
                $deleted++;
            }
        }

        $this->info("Cache cleared! Deleted {$deleted} files.");
    }

    public function getDescription(): string
    {
        return 'Clear the application cache';
    }
}
