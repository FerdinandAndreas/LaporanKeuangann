<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
             Dashboard Laporan Keuangan
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- Filter Periode --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-5 flex flex-wrap items-end gap-4">
                    <form method="GET" action="{{ route('dashboard') }}" class="flex flex-wrap items-end gap-3 w-full">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Filter Periode</label>
                            <select name="filter" id="filter-select" onchange="toggleCustomRange(this.value); this.form.submit()"
                                    class="block border-2 border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm font-medium bg-white px-3 py-2">
                                <option value="all"    {{ $filter === 'all'    ? 'selected' : '' }}>Semua Waktu</option>
                                <option value="today"  {{ $filter === 'today'  ? 'selected' : '' }}>Hari Ini</option>
                                <option value="week"   {{ $filter === 'week'   ? 'selected' : '' }}>Minggu Ini</option>
                                <option value="month"  {{ $filter === 'month'  ? 'selected' : '' }}>Bulan Ini</option>
                                <option value="custom" {{ $filter === 'custom' ? 'selected' : '' }}>Rentang Kustom</option>
                            </select>
                        </div>
                        <div id="custom-range" class="{{ $filter === 'custom' ? 'flex' : 'hidden' }} flex-wrap gap-3 items-end">
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                                <input type="date" name="start_date" value="{{ $startDate }}"
                                       class="block border-2 border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm px-3 py-2">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ $endDate }}"
                                       class="block border-2 border-slate-300 focus:border-amber-500 focus:ring-amber-500 rounded-lg shadow-sm text-sm px-3 py-2">
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-slate-900 font-bold text-sm rounded-lg shadow transition">
                                Terapkan
                            </button>
                        </div>

                        {{-- Export Buttons --}}
                        <div class="flex gap-2 ml-auto">
                            <a href="{{ route('reports.csv', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-lg shadow transition">
                                ⬇️ Export Excel
                            </a>
                            <a href="{{ route('reports.print', ['start_date' => $startDate, 'end_date' => $endDate]) }}"
                               target="_blank"
                               class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-700 hover:bg-slate-900 text-white font-bold text-sm rounded-lg shadow transition">
                                🖨️ Cetak PDF
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Ringkasan Finansial — kartu berwarna solid --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">

                {{-- Total Modal --}}
                <div class="bg-indigo-600 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-indigo-200">Total Modal Masuk</p>
                            <span class="text-3xl"></span>
                        </div>
                        <p class="text-2xl font-extrabold">Rp {{ number_format($totalCapital, 2, ',', '.') }}</p>
                        <a href="{{ route('capitals.index') }}"
                           class="mt-3 inline-block text-xs font-semibold text-indigo-200 hover:text-white underline underline-offset-2 transition">
                            Lihat detail →
                        </a>
                    </div>
                </div>

                {{-- Total Pembelian --}}
                <div class="bg-rose-600 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-rose-200">Total Pembelian (HPP)</p>
                            <span class="text-3xl"></span>
                        </div>
                        <p class="text-2xl font-extrabold">Rp {{ number_format($totalPurchases, 2, ',', '.') }}</p>
                        <a href="{{ route('purchases.index') }}"
                           class="mt-3 inline-block text-xs font-semibold text-rose-200 hover:text-white underline underline-offset-2 transition">
                            Lihat detail →
                        </a>
                    </div>
                </div>

                {{-- Total Penjualan --}}
                <div class="bg-emerald-600 rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">Total Penjualan (Omzet)</p>
                            <span class="text-3xl"></span>
                        </div>
                        <p class="text-2xl font-extrabold">Rp {{ number_format($totalSales, 2, ',', '.') }}</p>
                        <a href="{{ route('sales.index') }}"
                           class="mt-3 inline-block text-xs font-semibold text-emerald-200 hover:text-white underline underline-offset-2 transition">
                            Lihat detail →
                        </a>
                    </div>
                </div>

                {{-- Laba / Rugi Kotor --}}
                <div class="{{ $profit >= 0 ? 'bg-teal-600' : 'bg-orange-600' }} rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-widest {{ $profit >= 0 ? 'text-teal-200' : 'text-orange-200' }}">
                                Laba / Rugi Kotor
                            </p>
                            <span class="text-3xl">{{ $profit >= 0 ? '' : '' }}</span>
                        </div>
                        <p class="text-2xl font-extrabold">
                            {{ $profit >= 0 ? '+' : '' }}Rp {{ number_format($profit, 2, ',', '.') }}
                        </p>
                        <p class="mt-1 text-xs {{ $profit >= 0 ? 'text-teal-200' : 'text-orange-200' }}">Penjualan dikurangi Pembelian</p>
                    </div>
                </div>

                {{-- Modal Berjalan --}}
                <div class="sm:col-span-2 {{ $runningCapital >= 0 ? 'bg-slate-800' : 'bg-red-800' }} rounded-xl shadow-md overflow-hidden">
                    <div class="p-6 text-white">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-xs font-bold uppercase tracking-widest text-slate-300">Modal Berjalan (All Time)</p>
                            <span class="text-3xl"></span>
                        </div>
                        <p class="text-3xl font-extrabold">Rp {{ number_format($runningCapital, 2, ',', '.') }}</p>
                        <p class="mt-1 text-xs text-slate-400">= Total Modal + (Akumulasi Penjualan − Pembelian)</p>
                    </div>
                </div>
            </div>

            {{-- Grafik Tren Bulanan --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6">
                    <h3 class="text-base font-bold text-slate-800 mb-1"> Tren Keuangan (6 Bulan Terakhir)</h3>
                    <p class="text-xs text-slate-500 mb-4">Perbandingan pemasukan penjualan vs pengeluaran pembelian per bulan.</p>
                    <div style="height: 300px; position: relative;">
                        <canvas id="financialChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- Transaksi Terbaru --}}
            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-5">
                        <div>
                            <h3 class="text-base font-bold text-slate-800"> Transaksi Terbaru</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Transaksi pembelian dan penjualan terakhir dicatat.</p>
                        </div>
                        <div class="flex gap-2">
                            <a href="{{ route('purchases.create') }}"
                               class="inline-flex items-center gap-1 px-3 py-2 bg-rose-600 hover:bg-rose-700 text-white rounded-lg font-bold text-xs shadow transition">
                                🛒 + Pembelian
                            </a>
                            <a href="{{ route('sales.create') }}"
                               class="inline-flex items-center gap-1 px-3 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-bold text-xs shadow transition">
                                🧾 + Penjualan
                            </a>
                        </div>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Jenis</th>
                                    <th scope="col" class="px-5 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-5 py-3 text-right text-xs font-bold text-slate-300 uppercase tracking-wider">Total</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($recentTransactions as $trx)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-medium text-slate-700">{{ $trx->activity_date }}</td>
                                        <td class="px-5 py-3.5 whitespace-nowrap">
                                            @if($trx->activity_type === 'pembelian')
                                                <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-rose-100 text-rose-700 border border-rose-200">🛒 Pembelian</span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-100 text-emerald-700 border border-emerald-200">🧾 Penjualan</span>
                                            @endif
                                        </td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-semibold text-slate-800">{{ $trx->item_name }}</td>
                                        <td class="px-5 py-3.5 whitespace-nowrap text-sm font-extrabold text-right {{ $trx->activity_type === 'pembelian' ? 'text-rose-600' : 'text-emerald-600' }}">
                                            {{ $trx->activity_type === 'pembelian' ? '−' : '+' }} Rp {{ number_format($trx->total_price, 2, ',', '.') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-5 py-8 text-sm text-slate-400 text-center font-medium">
                                            Belum ada transaksi. Mulai catat pembelian atau penjualan pertama Anda.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <script>
        function toggleCustomRange(value) {
            const el = document.getElementById('custom-range');
            if (value === 'custom') {
                el.classList.remove('hidden');
                el.classList.add('flex');
            } else {
                el.classList.remove('flex');
                el.classList.add('hidden');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const ctx = document.getElementById('financialChart').getContext('2d');
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: {!! $chartMonths !!},
                    datasets: [
                        {
                            label: 'Pemasukan Penjualan',
                            data: {!! $chartSales !!},
                            borderColor: 'rgb(5, 150, 105)',
                            backgroundColor: 'rgba(5, 150, 105, 0.12)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgb(5, 150, 105)',
                            pointRadius: 5,
                            fill: true,
                            tension: 0.4
                        },
                        {
                            label: 'Pengeluaran Pembelian',
                            data: {!! $chartPurchases !!},
                            borderColor: 'rgb(225, 29, 72)',
                            backgroundColor: 'rgba(225, 29, 72, 0.10)',
                            borderWidth: 3,
                            pointBackgroundColor: 'rgb(225, 29, 72)',
                            pointRadius: 5,
                            fill: true,
                            tension: 0.4
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            labels: {
                                font: { weight: 'bold', size: 13 },
                                color: '#334155'
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.dataset.label || '';
                                    if (label) label += ': ';
                                    if (context.parsed.y !== null) {
                                        label += 'Rp ' + new Intl.NumberFormat('id-ID').format(context.parsed.y);
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: { color: 'rgba(148, 163, 184, 0.3)' },
                            ticks: {
                                color: '#64748b',
                                font: { weight: '600' },
                                callback: function(value) {
                                    return 'Rp ' + new Intl.NumberFormat('id-ID').format(value);
                                }
                            }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { color: '#64748b', font: { weight: '600' } }
                        }
                    }
                }
            });
        });
    </script>
</x-app-layout>
