<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class OptimizeImages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'images:optimize';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize portfolio images into AVIF, WebP, and responsive sizes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting portfolio image optimization...');
        
        $scriptPath = base_path('database/optimize_images.py');
        if (!file_exists($scriptPath)) {
            $this->error("Optimization script not found at {$scriptPath}");
            return Command::FAILURE;
        }

        $process = new Process(['python3', $scriptPath]);
        $process->setTimeout(300);
        
        $process->run(function ($type, $buffer) {
            $this->output->write($buffer);
        });

        if ($process->isSuccessful()) {
            $this->info('All portfolio images successfully optimized to AVIF/WebP responsive formats.');
            return Command::SUCCESS;
        }

        $this->error('Failed to optimize images: ' . $process->getErrorOutput());
        return Command::FAILURE;
    }
}
