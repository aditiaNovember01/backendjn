<?php

namespace App\Filament\Pages;

use App\Models\Krs;
use App\Models\Mahasiswa;
use App\Models\Sem;
use App\Models\Prodi;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Builder;

class LaporanAkademik extends Page implements HasTable, HasForms
{
    use InteractsWithTable, InteractsWithForms;

    protected static ?string $navigationIcon  = null;
    protected static ?string $navigationLabel = 'Laporan Akademik';
    protected static ?string $navigationGroup = 'Laporan';
    protected static ?int    $navigationSort  = 11;
    protected static string  $view            = 'filament.pages.laporan-akademik';

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role, ['admin', 'pimpinan']);
    }

    // ── Mode laporan: mahasiswa | krs | nilai ─────────────────
    public string $mode = 'mahasiswa';

    public function setMode(string $mode): void
    {
        $this->mode = $mode;
        $this->resetTable();
    }

    // ── Table ─────────────────────────────────────────────────
    public function table(Table $table): Table
    {
        return match($this->mode) {
            'krs'   => $this->tableKrs($table),
            'nilai' => $this->tableNilai($table),
            default => $this->tableMahasiswa($table),
        };
    }

    // ── Tab 1: Ringkasan per Mahasiswa (IPK) ──────────────────
    private function tableMahasiswa(Table $table): Table
    {
        return $table
            ->query(Mahasiswa::query()->with(['prodi', 'stat']))
            ->defaultSort('mhsnobp')
            ->columns([
                TextColumn::make('mhsnobp')
                    ->label('NoBP')
                    ->searchable()->sortable(),
                TextColumn::make('mhsnama')
                    ->label('Nama')
                    ->searchable()->sortable(),
                TextColumn::make('prodi.prodinama')
                    ->label('Prodi')->sortable(),
                TextColumn::make('mhsangkatan')
                    ->label('Angkatan')->sortable(),
                TextColumn::make('stat.statnama')
                    ->label('Status')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'Aktif'     => 'success',
                        'Non-Aktif' => 'danger',
                        'Cuti'      => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('total_sks')
                    ->label('Total SKS')
                    ->getStateUsing(fn(Mahasiswa $r) =>
                        Krs::where('krsmhsnobp', $r->mhsnobp)
                            ->where('krshapus', 0)
                            ->whereNotNull('krsnilai')->where('krsnilai', '!=', '')
                            ->with('kelas.kurikulum.mataKuliah')
                            ->get()
                            ->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0)
                    ),
                TextColumn::make('ipk')
                    ->label('IPK')
                    ->getStateUsing(fn(Mahasiswa $r) => $this->hitungIPK($r->mhsnobp))
                    ->sortable(false),
            ])
            ->filters([
                SelectFilter::make('mhsprodiid')
                    ->label('Prodi')
                    ->options(Prodi::pluck('prodinama', 'prodiid')->toArray()),
                SelectFilter::make('mhsangkatan')
                    ->label('Angkatan')
                    ->options(
                        Mahasiswa::distinct()->orderByDesc('mhsangkatan')
                            ->pluck('mhsangkatan', 'mhsangkatan')->toArray()
                    ),
                SelectFilter::make('mhsstatid')
                    ->label('Status')
                    ->options([1=>'Aktif', 2=>'Non-Aktif', 3=>'Cuti']),
            ])
            ->headerActions([
                Action::make('export_mahasiswa')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->exportMahasiswaCsv()),
            ])
            ->paginated([25, 50, 100]);
    }

    // ── Tab 2: Laporan KRS per Semester ───────────────────────
    private function tableKrs(Table $table): Table
    {
        return $table
            ->query(
                Krs::query()
                    ->with(['mahasiswa.prodi', 'kelas.kurikulum.mataKuliah', 'semester'])
                    ->where('krshapus', 0)
            )
            ->defaultSort('krssem', 'desc')
            ->columns([
                TextColumn::make('krsmhsnobp')->label('NoBP')->searchable()->sortable(),
                TextColumn::make('mahasiswa.mhsnama')->label('Nama')->searchable(),
                TextColumn::make('mahasiswa.prodi.prodinama')->label('Prodi')->toggleable(),
                TextColumn::make('semester.semnama')->label('Semester')->sortable(),
                TextColumn::make('kelas.kurikulum.mataKuliah.mtknama')->label('Mata Kuliah')->searchable(),
                TextColumn::make('kelas.kurikulum.mataKuliah.mtksks')->label('SKS'),
                TextColumn::make('krsapproved')
                    ->label('Status KRS')
                    ->badge()
                    ->formatStateUsing(fn($state) => $state ? 'Approved' : 'Pending')
                    ->color(fn($state) => $state ? 'success' : 'warning'),
                TextColumn::make('krsnilai')->label('Nilai'),
                TextColumn::make('krsbobot')->label('Bobot'),
            ])
            ->filters([
                SelectFilter::make('krssem')
                    ->label('Semester')
                    ->options(Sem::orderByDesc('semid')->pluck('semnama', 'semid')->toArray()),
                SelectFilter::make('prodi')
                    ->label('Prodi')
                    ->options(Prodi::pluck('prodinama', 'prodiid')->toArray())
                    ->query(fn(Builder $q, array $d) =>
                        $d['value'] ? $q->whereHas('mahasiswa', fn($mq) => $mq->where('mhsprodiid', $d['value'])) : $q
                    ),
            ])
            ->headerActions([
                Action::make('export_krs')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->exportKrsCsv()),
            ])
            ->paginated([25, 50, 100]);
    }

    // ── Tab 3: Laporan Nilai & IPK per Semester ───────────────
    private function tableNilai(Table $table): Table
    {
        return $table
            ->query(
                Krs::query()
                    ->with(['mahasiswa.prodi', 'kelas.kurikulum.mataKuliah', 'semester'])
                    ->where('krshapus', 0)
                    ->whereNotNull('krsnilai')
                    ->where('krsnilai', '!=', '')
            )
            ->defaultSort('krssem', 'desc')
            ->columns([
                TextColumn::make('krsmhsnobp')->label('NoBP')->searchable()->sortable(),
                TextColumn::make('mahasiswa.mhsnama')->label('Nama')->searchable(),
                TextColumn::make('mahasiswa.prodi.prodinama')->label('Prodi')->toggleable(),
                TextColumn::make('semester.semnama')->label('Semester')->sortable(),
                TextColumn::make('kelas.kurikulum.mataKuliah.mtknama')->label('Mata Kuliah'),
                TextColumn::make('kelas.kurikulum.mataKuliah.mtksks')->label('SKS'),
                TextColumn::make('krsnilai')->label('Nilai')
                    ->badge()
                    ->color(fn($state) => match($state) {
                        'A' => 'success', 'B' => 'info',
                        'C' => 'warning', 'D','E' => 'danger',
                        default => 'gray',
                    }),
                TextColumn::make('krsbobot')->label('Bobot'),
                TextColumn::make('mutu')
                    ->label('Mutu')
                    ->getStateUsing(fn(Krs $r) =>
                        ($r->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($r->krsbobot ?? 0)
                    ),
            ])
            ->filters([
                SelectFilter::make('krssem')
                    ->label('Semester')
                    ->options(Sem::orderByDesc('semid')->pluck('semnama', 'semid')->toArray()),
                SelectFilter::make('prodi')
                    ->label('Prodi')
                    ->options(Prodi::pluck('prodinama', 'prodiid')->toArray())
                    ->query(fn(Builder $q, array $d) =>
                        $d['value'] ? $q->whereHas('mahasiswa', fn($mq) => $mq->where('mhsprodiid', $d['value'])) : $q
                    ),
                SelectFilter::make('krsnilai')
                    ->label('Nilai')
                    ->options(['A'=>'A','B'=>'B','C'=>'C','D'=>'D','E'=>'E']),
            ])
            ->headerActions([
                Action::make('export_nilai')
                    ->label('Export CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->action(fn() => $this->exportNilaiCsv()),
            ])
            ->paginated([25, 50, 100]);
    }

    // ── Helper IPK ────────────────────────────────────────────
    private function hitungIPK(string $nobp): string
    {
        $krs = Krs::where('krsmhsnobp', $nobp)
            ->where('krshapus', 0)
            ->whereNotNull('krsnilai')->where('krsnilai', '!=', '')
            ->with('kelas.kurikulum.mataKuliah')
            ->get();

        $totalSks  = $krs->sum(fn($k) => $k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0);
        $totalMutu = $krs->sum(fn($k) => ($k->kelas?->kurikulum?->mataKuliah?->mtksks ?? 0) * ($k->krsbobot ?? 0));

        return $totalSks > 0 ? number_format($totalMutu / $totalSks, 2) : '0.00';
    }

    // ── Export CSVs ───────────────────────────────────────────
    public function exportMahasiswaCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Mahasiswa::with(['prodi', 'stat'])->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['NoBP','Nama','Prodi','Angkatan','Status','Total SKS','IPK']);
            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->mhsnobp, $r->mhsnama,
                    $r->prodi?->prodinama, $r->mhsangkatan,
                    $r->stat?->statnama,
                    $this->hitungIPK($r->mhsnobp) ? $this->hitungIPK($r->mhsnobp) : '0.00',
                    $this->hitungIPK($r->mhsnobp),
                ]);
            }
            fclose($out);
        }, 'laporan-mahasiswa-'.date('Ymd').'.csv', ['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    public function exportKrsCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Krs::with(['mahasiswa.prodi','kelas.kurikulum.mataKuliah','semester'])
            ->where('krshapus', 0)->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['NoBP','Nama','Prodi','Semester','Kode MK','Nama MK','SKS','Status','Nilai','Bobot']);
            foreach ($rows as $r) {
                $mk = $r->kelas?->kurikulum?->mataKuliah;
                fputcsv($out, [
                    $r->krsmhsnobp, $r->mahasiswa?->mhsnama,
                    $r->mahasiswa?->prodi?->prodinama,
                    $r->semester?->semnama,
                    $mk?->mtkid, $mk?->mtknama, $mk?->mtksks,
                    $r->krsapproved ? 'Approved' : 'Pending',
                    $r->krsnilai, $r->krsbobot,
                ]);
            }
            fclose($out);
        }, 'laporan-krs-'.date('Ymd').'.csv', ['Content-Type'=>'text/csv; charset=UTF-8']);
    }

    public function exportNilaiCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $rows = Krs::with(['mahasiswa.prodi','kelas.kurikulum.mataKuliah','semester'])
            ->where('krshapus', 0)
            ->whereNotNull('krsnilai')->where('krsnilai', '!=', '')->get();
        return response()->streamDownload(function () use ($rows) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($out, ['NoBP','Nama','Prodi','Semester','Kode MK','Nama MK','SKS','Nilai','Bobot','Mutu']);
            foreach ($rows as $r) {
                $mk  = $r->kelas?->kurikulum?->mataKuliah;
                $sks = $mk?->mtksks ?? 0;
                fputcsv($out, [
                    $r->krsmhsnobp, $r->mahasiswa?->mhsnama,
                    $r->mahasiswa?->prodi?->prodinama,
                    $r->semester?->semnama,
                    $mk?->mtkid, $mk?->mtknama, $sks,
                    $r->krsnilai, $r->krsbobot, $sks * ($r->krsbobot ?? 0),
                ]);
            }
            fclose($out);
        }, 'laporan-nilai-'.date('Ymd').'.csv', ['Content-Type'=>'text/csv; charset=UTF-8']);
    }
}
