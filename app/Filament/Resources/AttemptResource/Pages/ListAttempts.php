<?php

namespace App\Filament\Resources\AttemptResource\Pages;

use App\Filament\Resources\AttemptResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAttempts extends ListRecords
{
    protected static string $resource = AttemptResource::class;

    protected function getHeaderActions(): array
    {
        return [

        ];
    }
}
