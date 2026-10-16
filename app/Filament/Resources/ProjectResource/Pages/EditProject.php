<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('optimizePictures')
                ->label('Re-optimize Pictures')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Optimize Project Pictures')
                ->modalDescription('This will convert all cover and gallery pictures for this project into responsive AVIF & WebP formats (400w, 800w, 1200w).')
                ->action(function () {
                    $project = $this->getRecord();
                    try {
                        $service = app(\App\Services\ProjectImageService::class);
                        $res = $service->optimizeProjectImages($project, force: true);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Project Pictures Optimized!')
                            ->body('Successfully converted ' . count($res) . ' image(s) to responsive AVIF and WebP.')
                            ->send();
                    } catch (\Throwable $e) {
                        \Filament\Notifications\Notification::make()
                            ->danger()
                            ->title('Optimization Failed')
                            ->body($e->getMessage())
                            ->send();
                    }
                }),
            Actions\DeleteAction::make(),
        ];
    }
}
