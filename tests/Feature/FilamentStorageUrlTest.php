<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource\Pages\EditProject;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class FilamentStorageUrlTest extends TestCase
{
    public function test_public_disk_generates_relative_storage_urls(): void
    {
        $url = Storage::disk('public')->url('test-image.png');
        $this->assertEquals('/storage/test-image.png', $url);
    }

    public function test_storage_fallback_route_serves_existing_file(): void
    {
        Storage::disk('public')->put('unit-test-file.txt', 'Hello Storage');

        $response = $this->get('/storage/unit-test-file.txt');
        $response->assertStatus(200);
        $this->assertEquals('Hello Storage', file_get_contents($response->getFile()->getPathname()));

        Storage::disk('public')->delete('unit-test-file.txt');
    }

    public function test_filament_file_upload_uses_relative_url_for_previews(): void
    {
        $project = Project::first();
        if (! $project) {
            $this->markTestSkipped('No project records found.');
        }

        $component = Livewire::test(EditProject::class, ['record' => $project->getKey()])->instance();
        $fileUpload = $component->form->getComponent(fn ($c) => $c instanceof \Filament\Forms\Components\FileUpload && $c->getName() === 'image');

        $uploadedFiles = $fileUpload->getUploadedFiles();
        $this->assertNotEmpty($uploadedFiles);

        $first = reset($uploadedFiles);
        $this->assertArrayHasKey('url', $first);
        $this->assertStringStartsWith('/storage/', $first['url']);
    }
}
