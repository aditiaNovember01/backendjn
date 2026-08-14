<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MahasiswaResource\Pages;
use App\Models\Mahasiswa;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class MahasiswaResource extends Resource
{
    protected static ?string $model = Mahasiswa::class;

    protected static ?string $navigationIcon  = 'heroicon-o-academic-cap';
    protected static ?string $navigationLabel = 'Data Mahasiswa';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel      = 'Mahasiswa';
    protected static ?int    $navigationSort  = 3;

    // Read-only — tidak ada create/edit dari admin panel ini
    public static function canCreate(): bool { return false; }
    public static function canEdit($record): bool { return false; }
    public static function canDelete($record): bool { return false; }

    public static function form(Form $form): Form
    {
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('mhsnobp')
                    ->label('NoBP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mhsnama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('prodi.prodinama')
                    ->label('Prodi')
                    ->sortable(),

                Tables\Columns\TextColumn::make('mhsangkatan')
                    ->label('Angkatan')
                    ->sortable(),

                Tables\Columns\TextColumn::make('stat.statnama')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'Aktif' => 'success',
                        'Cuti'  => 'warning',
                        default => 'danger',
                    }),

                Tables\Columns\TextColumn::make('mhstelp')
                    ->label('No HP')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('mhsemail')
                    ->label('Email')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('mhsprodiid')
                    ->label('Prodi')
                    ->relationship('prodi', 'prodinama'),

                Tables\Filters\SelectFilter::make('mhsangkatan')
                    ->label('Angkatan')
                    ->options(
                        Mahasiswa::distinct()
                            ->orderByDesc('mhsangkatan')
                            ->pluck('mhsangkatan', 'mhsangkatan')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('mhsstatid')
                    ->label('Status')
                    ->relationship('stat', 'statnama'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMahasiswa::route('/'),
            'view'  => Pages\ViewMahasiswa::route('/{record}'),
        ];
    }
}
