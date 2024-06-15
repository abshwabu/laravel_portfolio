<?php

namespace App\Filament\Resources\ExperienceTaskResource\Pages;

use App\Filament\Resources\ExperienceTaskResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditExperienceTask extends EditRecord
{
    protected static string $resource = ExperienceTaskResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
