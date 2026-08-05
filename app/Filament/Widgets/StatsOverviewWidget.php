<?php

namespace App\Filament\Widgets;

use App\Models\BuktiPembayaran;
use App\Models\Mahasiswa;
use App\Models\Pengumuman;
use App\Models\Setting;
use App\Models\Krs;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        $semId = Setting::semesterAktif();

        return [
            Stat::make('Mahasiswa Aktif', Mahasiswa::whereHas('stat', fn($q) => $q->where('statnama', 'Aktif'))->count())
                ->description('Total mahasiswa status Aktif')
                ->icon('heroicon-o-academic-cap')
                ->color('success'),

            Stat::make('Pembayaran Pending', BuktiPembayaran::pending()->count())
                ->description('Menunggu konfirmasi admin')
                ->icon('heroicon-o-clock')
                ->color('warning'),

            Stat::make('Pembayaran Dikonfirmasi', BuktiPembayaran::dikonfirmasi()->count())
                ->description('Semester aktif & semua semester')
                ->icon('heroicon-o-check-circle')
                ->color('success'),

            Stat::make('KRS Semester Ini', Krs::where('krssem', $semId)->where('krshapus', 0)->count())
                ->description('Total entri KRS semester aktif')
                ->icon('heroicon-o-document-text')
                ->color('info'),

            Stat::make('Pengumuman Aktif', Pengumuman::where('aktif', true)->count())
                ->description('Pengumuman yang tampil di mobile')
                ->icon('heroicon-o-megaphone')
                ->color('primary'),
        ];
    }
}
