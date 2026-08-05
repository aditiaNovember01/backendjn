<?php

namespace App\Filament\Resources\BuktiPembayaranResource\Pages;

use App\Filament\Resources\BuktiPembayaranResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListBuktiPembayaran extends ListRecords
{
    protected static string $resource = BuktiPembayaranResource::class;

    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua'),

            'pending' => Tab::make('Pending')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'pending'))
                ->badge(fn() => \App\Models\BuktiPembayaran::pending()->count()),

            'dikonfirmasi' => Tab::make('Dikonfirmasi')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'dikonfirmasi')),

            'ditolak' => Tab::make('Ditolak')
                ->modifyQueryUsing(fn(Builder $q) => $q->where('status', 'ditolak')),
        ];
    }
}
