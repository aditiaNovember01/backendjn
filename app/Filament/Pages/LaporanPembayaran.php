<?php

namespace App\Filament\Pages;

use App\Models\Registrasi;
use App\Models\Sem;
use App\Models\Prodi;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class LaporanPembayaran extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon  = null;
    protected static ?string $navigationLabel = 'Laporan Pembayaran';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int    $navigationSort  = 10;
    protected static string  $view            = 'filament.pages.laporan-pembayaran';

    // Hanya admin & pimpinan
    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'pimpinan']);
    }

    // ── Filter state ─────────────────────────────────────────
    public ?int    $filterSem   = null;
    public ?int    $filterProdi = null;

    public function mount(): void
    {
        // Default: tampilkan semua semester (null = tidak filter)
        // Pengguna bisa filter sendiri via tabel filter
        $this->filterSem   = null;
        $this->filterProdi = null;
    }

    // ── Ringkasan statistik ───────────────────────────────────
    public function getSummary(): array
    {
        $query = \App\Models\Registrasi::query()
            ->where('regnobukti', 'NOT LIKE', 'AUTO-%')
            ->where('regjumlahbayar', '>', 0);

        if ($this->filterSem)   $query->where('regsem', $this->filterSem);

        if ($this->filterProdi) {
            $query->whereHas('mahasiswa', fn($q) => $q->where('mhsprodiid', $this->filterProdi));
        }

        $totalMhs    = (clone $query)->distinct('regmhsnobp')->count('regmhsnobp');
        $totalNominal= (clone $query)->sum('regjumlahbayar');
        $pending     = \App\Models\BuktiPembayaran::where('status', 'pending')
            ->when($this->filterSem, fn($q) => $q->where('sppsem', $this->filterSem))
            ->count();

        return [
            'total_lunas'   => $totalMhs,
            'total_nominal' => $totalNominal,
            'pending'       => $pending,
        ];
    }

    // ── Table ─────────────────────────────────────────────────
    public function table(Table $table): Table
    {
        return $table
            ->query($this->getTableQuery())
            ->defaultSort('regtanggalbayar', 'desc')
            ->columns([
                TextColumn::make('regmhsnobp')
                    ->label('NoBP')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mahasiswa.mhsnama')
                    ->label('Nama Mahasiswa')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('mahasiswa.prodi.prodinama')
                    ->label('Prodi')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('mahasiswa.mhsangkatan')
                    ->label('Angkatan')
                    ->sortable()
                    ->toggleable(),

                TextColumn::make('semester.semnama')
                    ->label('Semester')
                    ->sortable(),

                TextColumn::make('regjumlahbayar')
                    ->label('Jumlah Bayar')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('regtanggalbayar')
                    ->label('Tanggal Bayar')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('regnobukti')
                    ->label('No Bukti')
                    ->searchable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('regsem')
                    ->label('Semester')
                    ->options(
                        Sem::orderByDesc('semid')->pluck('semnama', 'semid')->toArray()
                    )
                    ->default($this->filterSem),

                SelectFilter::make('prodi')
                    ->label('Program Studi')
                    ->options(Prodi::pluck('prodinama', 'prodiid')->toArray())
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('mahasiswa', fn($q) => $q->where('mhsprodiid', $data['value']));
                        }
                    }),
            ])
            ->headerActions([
                Action::make('export_csv')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->exportCsv()),
            ])
            ->paginated([25, 50, 100, 'all']);
    }

    protected function getTableQuery(): Builder
    {
        return Registrasi::query()
            ->with(['mahasiswa.prodi', 'semester'])
            ->where('regnobukti', 'NOT LIKE', 'AUTO-%')
            ->where('regjumlahbayar', '>', 0);
    }

    // ── Export CSV ────────────────────────────────────────────
    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = $this->getTableQuery()->get();

        $filename = 'laporan-pembayaran-' . date('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            // BOM untuk Excel
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['NoBP','Nama','Prodi','Angkatan','Semester','Jumlah Bayar','Tanggal Bayar','No Bukti']);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->regmhsnobp,
                    $r->mahasiswa?->mhsnama,
                    $r->mahasiswa?->prodi?->prodinama,
                    $r->mahasiswa?->mhsangkatan,
                    $r->semester?->semnama,
                    $r->regjumlahbayar,
                    $r->regtanggalbayar?->format('d/m/Y'),
                    $r->regnobukti,
                ]);
            }
            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
