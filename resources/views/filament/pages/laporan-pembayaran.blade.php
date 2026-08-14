<x-filament-panels::page>

    {{-- Header ringkasan statistik --}}
    @php $summary = $this->getSummary(); @endphp

    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-6">
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Lunas</p>
            <p class="mt-1 text-3xl font-bold text-green-600">{{ number_format($summary['total_lunas']) }}</p>
            <p class="text-xs text-gray-400 mt-1">mahasiswa</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Total Nominal</p>
            <p class="mt-1 text-3xl font-bold text-blue-600">Rp {{ number_format($summary['total_nominal'], 0, ',', '.') }}</p>
            <p class="text-xs text-gray-400 mt-1">terkonfirmasi</p>
        </div>
        <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
            <p class="text-sm text-gray-500 dark:text-gray-400">Menunggu Konfirmasi</p>
            <p class="mt-1 text-3xl font-bold text-orange-500">{{ number_format($summary['pending']) }}</p>
            <p class="text-xs text-gray-400 mt-1">bukti pembayaran</p>
        </div>
    </div>

    {{-- Tabel --}}
    {{ $this->table }}

</x-filament-panels::page>
