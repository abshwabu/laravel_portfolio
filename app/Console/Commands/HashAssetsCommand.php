<?php

namespace App\Console\Commands;

use App\Support\Asset;
use Illuminate\Console\Command;

class HashAssetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'assets:hash {--clean : Remove old hashed files before generating}';

    /**
     * The console command description.
     */
    protected $description = 'Generate content-hashed copies of static CSS and JS assets for immutable caching';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Generating content-hashed static assets...');

        $directories = [
            public_path('css'),
            public_path('js'),
        ];

        if ($this->option('clean')) {
            foreach ($directories as $dir) {
                if (! is_dir($dir)) {
                    continue;
                }
                foreach (glob("{$dir}/*.*.*") as $file) {
                    if (preg_match('/\.([0-9a-fA-F]{8,32})\.(css|js|svg)$/', $file)) {
                        @unlink($file);
                    }
                }
            }
        }

        $targets = [
            'css/main.css',
            'css/header.css',
            'css/home.css',
            'css/footer.css',
            'css/about.css',
            'css/projects.css',
            'css/photos.css',
            'js/main.js',
        ];

        foreach ($targets as $target) {
            $hashedUrl = Asset::hashed($target);
            $this->line("  ✓ {$target} -> " . basename($hashedUrl));
        }

        $this->info('All static assets hashed successfully.');
        return Command::SUCCESS;
    }
}
