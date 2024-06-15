<?php

namespace App\Filament\Resources\ExperienceTaskResource\Pages;

use App\Filament\Resources\ExperienceTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListExperienceTasks extends ListRecords
{
    protected static string $resource = ExperienceTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
