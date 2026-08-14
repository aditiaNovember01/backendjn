<x-filament-panels::page>

    {{-- Tab switcher --}}
    <div class="flex gap-2 mb-5">
        <button
            wire:click="setMode('mahasiswa')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
                'bg-blue-600 text-white shadow' => $mode === 'mahasiswa',
                'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' => $mode !== 'mahasiswa',
            ])>
            📊 Ringkasan Mahasiswa
        </button>
        <button
            wire:click="setMode('krs')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
                'bg-blue-600 text-white shadow' => $mode === 'krs',
                'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' => $mode !== 'krs',
            ])>
            📋 Laporan KRS
        </button>
        <button
            wire:click="setMode('nilai')"
            @class([
                'px-4 py-2 rounded-lg text-sm font-semibold transition-colors',
                'bg-blue-600 text-white shadow' => $mode === 'nilai',
                'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' => $mode !== 'nilai',
            ])>
            🏆 Laporan Nilai
        </button>
    </div>

    {{-- Label mode aktif --}}
    <div class="mb-4">
        @if($mode === 'mahasiswa')
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                Ringkasan Akademik per Mahasiswa (IPK & Total SKS)
            </h3>
        @elseif($mode === 'krs')
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                Laporan Kartu Rencana Studi (KRS) per Semester
            </h3>
        @else
            <h3 class="text-base font-semibold text-gray-700 dark:text-gray-200">
                Laporan Nilai & Transkrip per Semester
            </h3>
        @endif
    </div>

    {{-- Tabel --}}
    {{ $this->table }}

</x-filament-panels::page>
