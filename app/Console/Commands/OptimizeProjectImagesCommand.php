<?php

namespace App\Console\Commands;

use App\Models\Project;
use App\Services\ProjectImageService;
use Illuminate\Console\Command;

class OptimizeProjectImagesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'project:optimize
                            {id? : Optional project ID to optimize}
                            {--force : Force re-optimization even if variants already exist}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Optimize all project images and gallery screenshots into responsive AVIF and WebP formats';

    /**
     * Execute the console command.
     */
    public function handle(ProjectImageService $service): int
    {
        $id = $this->argument('id');
        $force = (bool) $this->option('force');

        $projects = $id ? Project::where('id', $id)->get() : Project::all();

        if ($projects->isEmpty()) {
            $this->warn('No projects found to optimize.');
            return Command::SUCCESS;
        }

        $this->info("Found {$projects->count()} project(s). Starting image optimization...");

        $totalImages = 0;
        foreach ($projects as $project) {
            $this->newLine();
            $this->info("Project #{$project->id}: {$project->title}");

            $allImages = $project->all_images;
            if (empty($allImages)) {
                $this->comment('  No images attached to this project.');
                continue;
            }

            $res = $service->optimizeProjectImages($project, $force);
            foreach ($res as $imgPath => $info) {
                $status = $info['status'] ?? 'optimized';
                $baseName = $info['base_name'] ?? basename($imgPath);
                $this->line("  ✓ [{$status}] {$imgPath} -> {$baseName} (AVIF + WebP @ 400w, 800w, 1200w)");
                $totalImages++;
            }
        }

        $this->newLine();
        $this->info("Successfully processed {$totalImages} project image(s) according to portfolio optimization rules.");

        return Command::SUCCESS;
    }
}
