<?php

namespace App\Filament\Resources\SettingResource\Pages;

use App\Filament\Resources\SettingResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSetting extends EditRecord
{
    protected static string $resource = SettingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('optimizeHero')
                ->label('Re-optimize Hero Image')
                ->icon('heroicon-o-sparkles')
                ->color('success')
                ->requiresConfirmation()
                ->modalHeading('Optimize Hero Image')
                ->modalDescription('This will regenerate responsive AVIF & WebP variants (400px & 700px) and update site favicons.')
                ->action(function () {
                    $setting = $this->getRecord();
                    if (! $setting || ! $setting->hero_gif) {
                        \Filament\Notifications\Notification::make()
                            ->warning()
                            ->title('No hero image set')
                            ->body('Please upload an image first.')
                            ->send();
                        return;
                    }

                    try {
                        $service = app(\App\Services\HeroImageService::class);
                        $res = $service->optimizeExisting($setting->hero_gif);

                        \Filament\Notifications\Notification::make()
                            ->success()
                            ->title('Hero Image Optimized!')
                            ->body('Generated responsive AVIF & WebP variants (400w & 700w) and updated site favicons.')
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
