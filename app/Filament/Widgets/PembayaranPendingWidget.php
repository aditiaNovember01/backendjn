<?php

namespace App\Filament\Widgets;

use App\Models\BuktiPembayaran;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PembayaranPendingWidget extends BaseWidget
{
    protected static ?string $heading  = 'Pembayaran Menunggu Konfirmasi';
    protected int|string|array $columnSpan = 'full';
    protected static ?int    $sort      = 2;

    public function table(Table $table): Table
    {
        return $table
            ->query(BuktiPembayaran::with(['mahasiswa.prodi', 'semester'])->pending()->latest())
            ->columns([
                Tables\Columns\TextColumn::make('mhsnobp')->label('NoBP'),
                Tables\Columns\TextColumn::make('mahasiswa.mhsnama')->label('Nama'),
                Tables\Columns\TextColumn::make('mahasiswa.prodi.prodinama')->label('Prodi'),
                Tables\Columns\TextColumn::make('semester.semnama')->label('Semester'),
                Tables\Columns\TextColumn::make('jumlah_bayar')->label('Jumlah')->money('IDR'),
                Tables\Columns\ImageColumn::make('file_compressed')
                    ->label('Bukti')->disk('public')->height(50)->width(65),
                Tables\Columns\TextColumn::make('created_at')->label('Upload')->dateTime('d M Y H:i'),
            ])
            ->actions([
                Tables\Actions\Action::make('lihat')
                    ->label('Detail')
                    ->url(fn(BuktiPembayaran $r) => route('filament.admin.resources.bukti-pembayarans.view', $r))
                    ->icon('heroicon-o-arrow-top-right-on-square'),
            ]);
    }
}
