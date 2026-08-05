<?php

namespace App\Filament\Resources\BuktiPembayaranResource\Pages;

use App\Filament\Resources\BuktiPembayaranResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewBuktiPembayaran extends ViewRecord
{
    protected static string $resource = BuktiPembayaranResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
