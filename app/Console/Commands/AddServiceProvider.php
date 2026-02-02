<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class AddServiceProvider extends Command
{
    protected $signature = 'config:add-provider {provider} {alias?} {facade?}';
    protected $description = 'Add a service provider and optional alias to config/app.php';

    public function handle()
    {
        $provider = $this->argument('provider');
        $alias = $this->argument('alias');
        $facade = $this->argument('facade');

        $configPath = config_path('app.php');
        $content = file_get_contents($configPath);

        // Add the provider
        if (!str_contains($content, $provider)) {
            $content = preg_replace(
                '/(\'providers\'\s*=>\s*\[\n)/',
                "$1        $provider::class,\n",
                $content
            );
            $this->info("Provider added: $provider");
        }

        // Add the alias
        if ($alias && $facade && !str_contains($content, $alias)) {
            $content = preg_replace(
                '/(\'aliases\'\s*=>\s*\[\n)/',
                "$1        '$alias' => $facade::class,\n",
                $content
            );
            $this->info("Alias added: $alias");
        }

        file_put_contents($configPath, $content);
    }
}
