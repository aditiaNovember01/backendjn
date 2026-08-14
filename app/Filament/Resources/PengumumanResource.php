<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PengumumanResource\Pages;
use App\Models\Pengumuman;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class PengumumanResource extends Resource
{
    protected static ?string $model = Pengumuman::class;

    protected static ?string $navigationIcon  = 'heroicon-o-megaphone';
    protected static ?string $navigationLabel = 'Pengumuman';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel      = 'Pengumuman';
    protected static ?int    $navigationSort  = 2;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make()->schema([
                Forms\Components\TextInput::make('judul')
                    ->label('Judul Pengumuman')
                    ->required()
                    ->maxLength(200)
                    ->columnSpanFull(),

                Forms\Components\RichEditor::make('isi')
                    ->label('Isi Pengumuman')
                    ->required()
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'bulletList', 'orderedList',
                        'link', 'blockquote',
                        'h2', 'h3',
                    ])
                    ->columnSpanFull(),

                Forms\Components\DatePicker::make('tgl_publish')
                    ->label('Tanggal Publish')
                    ->default(now())
                    ->helperText('Kosongkan untuk tidak terbatas waktu'),

                Forms\Components\Toggle::make('aktif')
                    ->label('Aktif')
                    ->default(true)
                    ->inline(false),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('judul')
                    ->label('Judul')
                    ->searchable()
                    ->limit(60),

                Tables\Columns\TextColumn::make('tgl_publish')
                    ->label('Tgl Publish')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\IconColumn::make('aktif')
                    ->label('Aktif')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('admin.name')
                    ->label('Dibuat oleh')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('aktif')
                    ->label('Status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Nonaktif'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('toggle_aktif')
                    ->label(fn(Pengumuman $r) => $r->aktif ? 'Nonaktifkan' : 'Aktifkan')
                    ->icon(fn(Pengumuman $r) => $r->aktif ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                    ->color(fn(Pengumuman $r) => $r->aktif ? 'warning' : 'success')
                    ->action(fn(Pengumuman $r) => $r->update(['aktif' => ! $r->aktif])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListPengumuman::route('/'),
            'create' => Pages\CreatePengumuman::route('/create'),
            'edit'   => Pages\EditPengumuman::route('/{record}/edit'),
        ];
    }
}
