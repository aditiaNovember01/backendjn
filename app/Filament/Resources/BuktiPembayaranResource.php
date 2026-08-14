<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BuktiPembayaranResource\Pages;
use App\Models\BuktiPembayaran;
use App\Models\Registrasi;
use App\Models\Sem;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BuktiPembayaranResource extends Resource
{
    protected static ?string $model = BuktiPembayaran::class;

    protected static ?string $navigationIcon  = 'heroicon-o-banknotes';
    protected static ?string $navigationLabel = 'Konfirmasi Pembayaran';
    protected static ?string $navigationGroup = 'Akademik';
    protected static ?string $modelLabel      = 'Bukti Pembayaran';
    protected static ?string $pluralModelLabel = 'Bukti Pembayaran';
    protected static ?int    $navigationSort  = 1;

    // Badge jumlah pending di sidebar
    public static function getNavigationBadge(): ?string
    {
        $count = BuktiPembayaran::pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'warning';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informasi Pembayaran')
                ->schema([
                    Forms\Components\TextInput::make('mhsnobp')
                        ->label('NoBP')
                        ->disabled(),
                    Forms\Components\TextInput::make('jumlah_bayar')
                        ->label('Jumlah Bayar')
                        ->prefix('Rp')
                        ->disabled(),
                    Forms\Components\Select::make('status')
                        ->label('Status')
                        ->options([
                            'pending'      => 'Pending',
                            'dikonfirmasi' => 'Dikonfirmasi',
                            'ditolak'      => 'Ditolak',
                        ])
                        ->required(),
                    Forms\Components\Textarea::make('catatan')
                        ->label('Catatan Penolakan')
                        ->rows(3)
                        ->helperText('Isi jika status Ditolak'),
                ])->columns(2),

            Forms\Components\Section::make('Bukti Pembayaran')
                ->schema([
                    Forms\Components\Placeholder::make('preview_bukti')
                        ->label('Preview Bukti (Compressed)')
                        ->content(function ($record) {
                            if (! $record?->file_compressed) {
                                return new \Illuminate\Support\HtmlString('<p class="text-gray-500 text-sm">Tidak ada gambar.</p>');
                            }
                            $url = \Illuminate\Support\Facades\Storage::disk('public')->url($record->file_compressed);
                            return new \Illuminate\Support\HtmlString(
                                '<img src="' . e($url) . '" alt="Bukti Pembayaran"
                                      style="max-height:300px;max-width:100%;border-radius:8px;border:1px solid #e5e7eb;" />'
                            );
                        }),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('mhsnobp')
                    ->label('NoBP')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mahasiswa.mhsnama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('mahasiswa.prodi.prodinama')
                    ->label('Prodi')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('semester.semnama')
                    ->label('Semester')
                    ->sortable(),

                Tables\Columns\TextColumn::make('jumlah_bayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('tipe_bayar')
                    ->label('Tipe Bayar')
                    ->colors([
                        'info'    => 'penuh',
                        'warning' => 'cicilan1',
                        'primary' => 'cicilan2',
                        'success' => 'cicilan3',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'penuh'    => 'Penuh',
                        'cicilan1' => 'Cicilan 1',
                        'cicilan2' => 'Cicilan 2',
                        'cicilan3' => 'Cicilan 3',
                        default    => $state ?? 'Penuh',
                    }),

                Tables\Columns\ImageColumn::make('file_compressed')
                    ->label('Bukti')
                    ->disk('public')
                    ->height(60)
                    ->width(80)
                    ->extraImgAttributes(['style' => 'object-fit:cover;border-radius:4px;']),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'dikonfirmasi',
                        'danger'  => 'ditolak',
                    ])
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Upload')
                    ->dateTime('d M Y H:i')
                    ->sortable(),

                Tables\Columns\TextColumn::make('confirmed_at')
                    ->label('Dikonfirmasi')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'pending'      => 'Pending',
                        'dikonfirmasi' => 'Dikonfirmasi',
                        'ditolak'      => 'Ditolak',
                    ]),

                Tables\Filters\SelectFilter::make('sppsem')
                    ->label('Semester')
                    ->options(
                        Sem::orderByDesc('semid')
                            ->pluck('semnama', 'semid')
                            ->toArray()
                    ),

                Tables\Filters\SelectFilter::make('prodi')
                    ->label('Prodi')
                    ->relationship('mahasiswa.prodi', 'prodinama'),
            ])
            ->actions([
                // Konfirmasi: approve pembayaran
                Tables\Actions\Action::make('konfirmasi')
                    ->label('Konfirmasi')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn(BuktiPembayaran $record) => $record->status === 'pending')
                    ->requiresConfirmation()
                    ->modalHeading('Konfirmasi Pembayaran')
                    ->modalDescription(fn(BuktiPembayaran $record) =>
                        "Konfirmasi pembayaran {$record->mhsnobp} - {$record->mahasiswa?->mhsnama} sebesar Rp " .
                        number_format($record->jumlah_bayar, 0, ',', '.') . '?'
                    )
                    ->action(function (BuktiPembayaran $record) {
                        DB::transaction(function () use ($record) {
                            // Tagihan SPP
                            $sppTagihan = \App\Models\Spp::where('sppmhsnobp', $record->mhsnobp)
                                ->where('sppsem', $record->sppsem)
                                ->value('spptagihan') ?? 0;

                            // Jika jumlah bayar kurang dari total tagihan dan tipe diset penuh, auto-adjust tipe_bayar ke cicilan1
                            if ($record->jumlah_bayar < $sppTagihan && $record->tipe_bayar === 'penuh') {
                                $record->tipe_bayar = 'cicilan1';
                            }

                            // Update status bukti
                            $record->update([
                                'status'       => 'dikonfirmasi',
                                'tipe_bayar'   => $record->tipe_bayar,
                                'confirmed_by' => Auth::id(),
                                'confirmed_at' => now(),
                                'catatan'      => null,
                            ]);

                            // Hitung total akumulasi pembayaran yang dikonfirmasi untuk semester ini
                            $totalDikonfirmasi = BuktiPembayaran::where('mhsnobp', $record->mhsnobp)
                                ->where('sppsem', $record->sppsem)
                                ->where('status', 'dikonfirmasi')
                                ->sum('jumlah_bayar');

                            // Hanya buat/update Registrasi (Lunas) jika total akumulasi pembayaran >= total tagihan SPP
                            if ($totalDikonfirmasi >= $sppTagihan) {
                                $noBukti = 'JN/' . now()->format('Y/m') . '/' . str_pad($record->id, 6, '0', STR_PAD_LEFT);

                                Registrasi::updateOrCreate(
                                    [
                                        'regmhsnobp' => $record->mhsnobp,
                                        'regsem'     => $record->sppsem,
                                    ],
                                    [
                                        'regjumlahbayar'  => (int) $totalDikonfirmasi,
                                        'regtanggalbayar' => now()->toDateString(),
                                        'reguserinput'    => 'admin:' . Auth::id(),
                                        'regnobukti'      => $noBukti,
                                    ]
                                );
                            }
                        });

                        Notification::make()
                            ->title('Pembayaran berhasil dikonfirmasi')
                            ->success()
                            ->send();
                    }),

                // Tolak pembayaran
                Tables\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn(BuktiPembayaran $record) => $record->status === 'pending')
                    ->form([
                        Forms\Components\Textarea::make('catatan')
                            ->label('Alasan Penolakan')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (BuktiPembayaran $record, array $data) {
                        $record->update([
                            'status'       => 'ditolak',
                            'catatan'      => $data['catatan'],
                            'confirmed_by' => Auth::id(),
                            'confirmed_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Pembayaran ditolak')
                            ->warning()
                            ->send();
                    }),

                // Lihat gambar full
                Tables\Actions\Action::make('lihat_bukti')
                    ->label('Lihat Bukti')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn(BuktiPembayaran $record) => $record->file_url)
                    ->openUrlInNewTab(),

                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBuktiPembayaran::route('/'),
            'view'  => Pages\ViewBuktiPembayaran::route('/{record}'),
        ];
    }
}
