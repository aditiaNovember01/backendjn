<?php

namespace App\Filament\Resources\MahasiswaResource\Pages;

use App\Filament\Resources\MahasiswaResource;
use Filament\Infolists;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;

class ViewMahasiswa extends ViewRecord
{
    protected static string $resource = MahasiswaResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Infolists\Components\Section::make('Data Pribadi')->schema([
                Infolists\Components\TextEntry::make('mhsnobp')->label('NoBP'),
                Infolists\Components\TextEntry::make('mhsnama')->label('Nama'),
                Infolists\Components\TextEntry::make('mhsnik')->label('NIK'),
                Infolists\Components\TextEntry::make('mhsjkl')
                    ->label('Jenis Kelamin')
                    ->formatStateUsing(fn($s) => $s === 'L' ? 'Laki-Laki' : 'Perempuan'),
                Infolists\Components\TextEntry::make('mhstmplhr')->label('Tempat Lahir'),
                Infolists\Components\TextEntry::make('mhstgllahir')
                    ->label('Tanggal Lahir')
                    ->date('d F Y'),
                Infolists\Components\TextEntry::make('mhsalamat')->label('Alamat'),
                Infolists\Components\TextEntry::make('mhstelp')->label('No HP'),
                Infolists\Components\TextEntry::make('mhsemail')->label('Email'),
                Infolists\Components\TextEntry::make('agama.agamanama')->label('Agama'),
            ])->columns(2),

            Infolists\Components\Section::make('Data Akademik')->schema([
                Infolists\Components\TextEntry::make('prodi.prodinama')->label('Prodi'),
                Infolists\Components\TextEntry::make('mhsangkatan')->label('Angkatan'),
                Infolists\Components\TextEntry::make('mhssemidmasuk')->label('Semester Masuk'),
                Infolists\Components\TextEntry::make('jalur.jalurnama')->label('Jalur'),
                Infolists\Components\TextEntry::make('stat.statnama')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'Aktif' => 'success',
                        'Cuti'  => 'warning',
                        default => 'danger',
                    }),
                Infolists\Components\TextEntry::make('mhskel')
                    ->label('Kelas Biaya')
                    ->formatStateUsing(fn($s) => match($s) {
                        'R' => 'Reguler',
                        'E' => 'Reguler Malam',
                        'K' => 'KIP Kuliah',
                        default => $s,
                    }),
            ])->columns(2),

            Infolists\Components\Section::make('Data Orang Tua')->schema([
                Infolists\Components\TextEntry::make('mhsortu')->label('Nama Ayah'),
                Infolists\Components\TextEntry::make('mhsibu')->label('Nama Ibu'),
            ])->columns(2),
        ]);
    }
}
