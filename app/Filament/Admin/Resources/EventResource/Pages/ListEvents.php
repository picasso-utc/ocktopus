<?php

namespace App\Filament\Admin\Resources\EventResource\Pages;

use App\Filament\Admin\Resources\EventResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Support\Facades\File;

class ListEvents extends ListRecords
{
    protected static string $resource = EventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
    
    public function bootLivewireComponents()
    {
        $componentsDirectory = app_path('Filament/Admin/Resources/EventResource/Components');
        
        if (!File::isDirectory($componentsDirectory)) {
            File::makeDirectory($componentsDirectory, 0755, true);
        }
        
        $viewDirectory = resource_path('views/filament/admin/resources/event-resource/components');
        
        if (!File::isDirectory($viewDirectory)) {
            File::makeDirectory($viewDirectory, 0755, true);
        }
    }
}