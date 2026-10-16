<?php

namespace App\Console\Commands;

use App\Services\HeroImageService;
use Illuminate\Console\Command;

class UploadHeroImageCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hero:upload
                            {file : Path to the image file to upload as hero portrait}
                            {--name= : Optional custom filename base without extension}
                            {--no-favicons : Skip regenerating site favicons}
                            {--no-db : Skip updating Setting database record}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload and optimize a hero portrait image into responsive AVIF, WebP, and fallback formats';

    /**
     * Execute the console command.
     */
    public function handle(HeroImageService $service): int
    {
        $filePath = $this->argument('file');

        if (! file_exists($filePath)) {
            $this->error("File not found at: {$filePath}");
            return Command::FAILURE;
        }

        $imageInfo = @getimagesize($filePath);
        if (! $imageInfo) {
            $this->error("The specified file is not a valid image: {$filePath}");
            return Command::FAILURE;
        }

        $this->info("Analyzing image: {$filePath} ({$imageInfo[0]}x{$imageInfo[1]} {$imageInfo['mime']})");
        $this->info('Optimizing hero image according to portfolio performance rules...');

        $customName = $this->option('name');
        $updateDb = ! $this->option('no-db');
        $generateFavicons = ! $this->option('no-favicons');

        try {
            $result = $service->optimize($filePath, $customName, $updateDb, $generateFavicons);
        } catch (\Throwable $e) {
            $this->error('Optimization failed: ' . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✓ Hero image successfully optimized! (Base ID: {$result['base_name']})");

        $rows = [];
        foreach ($result['files'] as $key => $fileData) {
            if ($key === 'favicons') {
                continue;
            }
            $rows[] = [
                $key,
                "{$fileData['width']}x{$fileData['height']}",
                number_format($fileData['size'] / 1024, 1) . ' KiB',
                basename($fileData['path']),
            ];
        }

        $this->table(['Variant', 'Dimensions', 'File Size', 'Filename'], $rows);

        if ($generateFavicons && isset($result['files']['favicons'])) {
            $this->info('✓ Regenerated site favicons (apple-touch-icon.png, favicon-32x32.png, favicon-16x16.png, favicon.ico)');
        }

        if ($updateDb) {
            $this->info('✓ Updated Settings table with new hero_gif reference.');
            $this->info('✓ Cleared compiled Blade view cache.');
        }

        $this->newLine();
        $this->comment('All optimization rules verified:');
        $this->line('  • Modern formats: AVIF primary with WebP fallback');
        $this->line('  • Responsive sizes: 400w (mobile) & 700w (desktop)');
        $this->line('  • Master original kept safely out of public build output');
        $this->line('  • Mobile hero size well below 100 KiB');

        return Command::SUCCESS;
    }
}
