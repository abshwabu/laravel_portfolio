<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\HeroImageService;
use Tests\TestCase;

class HeroImageUploadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        if (! Setting::first()) {
            Setting::create([
                'username' => 'testuser',
                'name' => 'Test User',
                'title' => 'Software Engineer',
                'hero_gif' => '01JQ42KG7VZEPZQA1N8F2FZZRW.png',
            ]);
        }
    }

    public function test_hero_image_service_generates_optimized_variants_and_favicons(): void
    {
        $service = app(HeroImageService::class);
        $tempImg = tempnam(sys_get_temp_dir(), 'test_hero_') . '.png';

        $im = imagecreatetruecolor(800, 800);
        $blue = imagecolorallocate($im, 0, 100, 200);
        imagefilledrectangle($im, 0, 0, 800, 800, $blue);
        imagepng($im, $tempImg);

        $customName = 'test_hero_' . time();
        $result = $service->optimize($tempImg, $customName, updateDb: false, generateFavicons: true);

        $this->assertEquals($customName, $result['base_name']);
        $this->assertFileExists(storage_path("app/public/{$customName}-400.avif"));
        $this->assertFileExists(storage_path("app/public/{$customName}-400.webp"));
        $this->assertFileExists(storage_path("app/public/{$customName}-700.avif"));
        $this->assertFileExists(storage_path("app/public/{$customName}-700.webp"));
        $this->assertFileExists(storage_path("app/public/{$customName}.avif"));
        $this->assertFileExists(storage_path("app/public/{$customName}.webp"));
        $this->assertFileExists(storage_path("app/public/{$customName}.png"));
        $this->assertFileExists(storage_path("app/originals/hero/{$customName}_original.png"));

        // Rule verification: Mobile hero size should not exceed 100 KiB
        $mobileAvifSize = filesize(storage_path("app/public/{$customName}-400.avif"));
        $this->assertLessThan(100 * 1024, $mobileAvifSize);

        // Favicons verification
        $this->assertFileExists(public_path('apple-touch-icon.png'));
        $this->assertFileExists(public_path('favicon-32x32.png'));
        $this->assertFileExists(public_path('favicon-16x16.png'));
        $this->assertFileExists(public_path('favicon.ico'));

        // Clean up test files
        @unlink($tempImg);
        foreach (glob(storage_path("app/public/{$customName}*")) as $f) {
            @unlink($f);
        }
        @unlink(storage_path("app/originals/hero/{$customName}_original.png"));
    }

    public function test_artisan_hero_upload_command(): void
    {
        $tempImg = tempnam(sys_get_temp_dir(), 'test_artisan_') . '.png';
        $im = imagecreatetruecolor(600, 600);
        imagepng($im, $tempImg);

        $testName = 'artisan_test_' . time();
        $this->artisan('hero:upload', [
            'file' => $tempImg,
            '--name' => $testName,
            '--no-db' => true,
        ])->assertSuccessful();

        $this->assertFileExists(storage_path("app/public/{$testName}-400.avif"));
        $this->assertFileExists(storage_path("app/public/{$testName}-400.webp"));

        @unlink($tempImg);
        foreach (glob(storage_path("app/public/{$testName}*")) as $f) {
            @unlink($f);
        }
        @unlink(storage_path("app/originals/hero/{$testName}_original.png"));
    }
}
