<?php

namespace App\Filament\Admin\Resources\Assets\Pages;

use App\Filament\Admin\Resources\Assets\AssetResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAssets extends ListRecords
{
    protected static string $resource = AssetResource::class;

    protected function getHeaderActions(): array
    {
        return [
            \Filament\Actions\Action::make('sync')
                ->label('Sync Assets')
                ->icon('heroicon-o-arrow-path')
                ->color('info')
                ->action(function () {
                    \Illuminate\Support\Facades\Artisan::call('db:seed', ['--class' => 'AssetSeeder']);
                    \Filament\Notifications\Notification::make()
                        ->title('Assets synced successfully')
                        ->success()
                        ->send();
                }),
            CreateAction::make(),

        ];
    }
}
