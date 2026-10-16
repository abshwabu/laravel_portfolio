<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Services\ProjectImageService;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

class ProjectGalleryTest extends TestCase
{
    protected string $testDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->testDir = storage_path('app/public/projects');
        if (! File::isDirectory($this->testDir)) {
            File::makeDirectory($this->testDir, 0755, true);
        }
    }

    public function test_project_image_service_generates_optimized_variants(): void
    {
        $service = app(ProjectImageService::class);
        $testFileName = 'test_gallery_' . time() . '.png';
        $fullPath = $this->testDir . '/' . $testFileName;

        // Create a 1600x900 test image
        $im = imagecreatetruecolor(1600, 900);
        $purple = imagecolorallocate($im, 108, 92, 231);
        imagefilledrectangle($im, 0, 0, 1600, 900, $purple);
        imagepng($im, $fullPath);
        imagedestroy($im);

        $result = $service->optimizeSingleImage('projects/' . $testFileName);

        $this->assertEquals('optimized', $result['status']);
        $baseName = pathinfo($testFileName, PATHINFO_FILENAME);

        // Verify variants exist (400, 800, 1200)
        foreach ([400, 800, 1200] as $w) {
            $this->assertFileExists("{$this->testDir}/{$baseName}-{$w}.avif");
            $this->assertFileExists("{$this->testDir}/{$baseName}-{$w}.webp");
        }
        $this->assertFileExists("{$this->testDir}/{$baseName}.avif");
        $this->assertFileExists("{$this->testDir}/{$baseName}.webp");
        $this->assertFileExists(storage_path("app/originals/projects/{$baseName}_original.png"));

        // Verify optimized image data structure
        $data = $service->getOptimizedImageData('projects/' . $testFileName);
        $this->assertArrayHasKey('avif_srcset', $data);
        $this->assertArrayHasKey('webp_srcset', $data);
        $this->assertArrayHasKey('src', $data);
        $this->assertArrayHasKey('thumb', $data);

        // Clean up
        foreach (glob("{$this->testDir}/{$baseName}*") as $f) {
            @unlink($f);
        }
        @unlink(storage_path("app/originals/projects/{$baseName}_original.png"));
    }

    public function test_project_model_all_images_and_all_images_data_accessors(): void
    {
        $project = new Project([
            'title' => 'Sample Multi Image Project',
            'image' => 'projects/cover.png',
            'images' => [
                'projects/gallery1.png',
                'projects/gallery2.png',
            ],
            'tags' => ['PHP', 'Laravel'],
            'category' => 'Web App',
            'description' => 'Test description',
        ]);

        $allImages = $project->all_images;
        $this->assertCount(3, $allImages);
        $this->assertEquals('projects/cover.png', $allImages[0]);
        $this->assertEquals('projects/gallery1.png', $allImages[1]);
        $this->assertEquals('projects/gallery2.png', $allImages[2]);

        $allData = $project->all_images_data;
        $this->assertCount(3, $allData);
        $this->assertEquals('Sample Multi Image Project - Screenshot 1', $allData[0]['alt']);
        $this->assertEquals('Sample Multi Image Project - Screenshot 2', $allData[1]['alt']);
        $this->assertEquals('Sample Multi Image Project - Screenshot 3', $allData[2]['alt']);
    }

    public function test_artisan_project_optimize_command(): void
    {
        $testFileName = 'artisan_proj_' . time() . '.png';
        $fullPath = $this->testDir . '/' . $testFileName;

        $im = imagecreatetruecolor(800, 450);
        imagepng($im, $fullPath);
        imagedestroy($im);

        $project = Project::create([
            'title' => 'Artisan Test Project',
            'image' => 'projects/' . $testFileName,
            'images' => [],
            'tags' => ['Laravel'],
            'category' => 'Test',
            'description' => 'Testing artisan command',
        ]);

        $this->artisan('project:optimize', [
            'id' => $project->id,
            '--force' => true,
        ])->assertSuccessful();

        $baseName = pathinfo($testFileName, PATHINFO_FILENAME);
        $this->assertFileExists("{$this->testDir}/{$baseName}-400.avif");
        $this->assertFileExists("{$this->testDir}/{$baseName}-400.webp");

        // Clean up
        $project->delete();
        foreach (glob("{$this->testDir}/{$baseName}*") as $f) {
            @unlink($f);
        }
        @unlink(storage_path("app/originals/projects/{$baseName}_original.png"));
    }
}
