<?php

namespace App\Filament\Admin\Resources\MembersResource\Pages;

use App\Filament\Admin\Resources\MembersResource;
use Filament\Resources\Pages\ManageRecords;

class ManageMembers extends ManageRecords
{
    protected static string $resource = MembersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }
}
