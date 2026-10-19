<?php

namespace Tests\Feature;

use App\Filament\Resources\ProjectResource;
use App\Filament\Resources\ProjectResource\Pages\ListProjects;
use App\Models\Project;
use Filament\Tables\Table;
use Livewire\Livewire;
use Tests\TestCase;

class ProjectResourceTest extends TestCase
{
    public function test_github_url_column_handles_null_value_without_error(): void
    {
        $table = ProjectResource::table(Table::make(Livewire::test(ListProjects::class)->instance()));
        $column = $table->getColumn('github_url');

        $projectWithNullUrl = new Project(['github_url' => null]);
        $column->record($projectWithNullUrl);
        $this->assertNull($column->getUrl());

        $projectWithEmptyUrl = new Project(['github_url' => '']);
        $column->record($projectWithEmptyUrl);
        $this->assertNull($column->getUrl());

        $projectWithValidUrl = new Project(['github_url' => 'https://github.com/example/repo']);
        $column->record($projectWithValidUrl);
        $this->assertEquals('https://github.com/example/repo', $column->getUrl());
    }
}
