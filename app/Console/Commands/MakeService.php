<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class MakeService extends Command
{
    protected $signature = 'make:service {name}';
    protected $description = 'Create a new service class';

    public function handle()
    {
        $name = $this->argument('name');
        $filename = app_path("Services/{$name}.php");

        $this->makeDirectory($filename);
        File::put($filename, $this->buildServiceClass($name));

        $this->info("Service created successfully: {$filename}");
    }

    protected function makeDirectory($filename)
    {
        $directory = dirname($filename);

        if (!File::isDirectory($directory)) {
            File::makeDirectory($directory, 0755, true, true);
        }
    }

    protected function buildServiceClass($name)
    {
        return "<?php\n\nnamespace App\Services;\n\nclass {$name}\n{\n    // Your service logic goes here\n}\n";
    }
}
