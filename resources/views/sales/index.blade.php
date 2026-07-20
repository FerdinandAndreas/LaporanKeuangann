<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            🧾 Transaksi Penjualan (Stok Keluar)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg font-medium">
                    ✅ {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-800 rounded-r-lg font-medium">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            <!-- Sales Summary Card -->
            <div class="bg-emerald-700 rounded-xl shadow-md text-white mb-6 p-6 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-emerald-200">Total Pemasukan Penjualan</p>
                    <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($totalSales, 2, ',', '.') }}</p>
                </div>
                <a href="{{ route('sales.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-emerald-700 font-bold text-sm rounded-lg shadow hover:bg-emerald-50 transition">
                    🧾 + Catat Penjualan
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6">
                    <div class="flex flex-wrap justify-between items-center gap-4 mb-5">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">🗂️ Riwayat Penjualan Barang</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Semua transaksi penjualan / stok keluar.</p>
                        </div>
                        <button type="submit" form="batch-receipt-form"
                                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow transition">
                            📄 Cetak Nota Gabungan
                        </button>
                    </div>

                    <!-- Search & Filter Form -->
                    <form method="GET" action="{{ route('sales.index') }}"
                          class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Cari Nama Barang</label>
                            <input type="text" id="search" name="search" value="{{ request('search') }}" placeholder="Misal: Beras IR64"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Pembeli</label>
                            <input type="text" id="buyer" name="buyer" value="{{ request('buyer') }}" placeholder="Nama pembeli"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                            <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}"
                                       class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                            </div>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition self-end shadow">
                                Cari
                            </button>
                            <a href="{{ route('sales.index') }}" class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-bold transition self-end">
                                Reset
                            </a>
                        </div>
                    </form>

                    <form id="batch-receipt-form" method="GET" action="{{ route('sales.batch-receipt') }}" target="_blank">
                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-slate-300 uppercase tracking-wider w-10">
                                        <input type="checkbox" id="select-all-sales" onclick="toggleSelectAll(this)"
                                               class="rounded border-slate-500 text-indigo-400 focus:ring-indigo-500 bg-slate-700">
                                    </th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Jumlah (Qty)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Harga Satuan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Total Harga</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Pembeli</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($sales as $sale)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-4 py-4 whitespace-nowrap text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $sale->id }}"
                                                   class="sale-checkbox rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">{{ $sale->sale_date->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                            {{ $sale->item_name }}
                                            @if($sale->product)
                                                <span class="text-xs font-normal text-slate-400">({{ $sale->product->name }})</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ number_format($sale->quantity, 2, ',', '.') }} {{ $sale->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">Rp {{ number_format($sale->price_per_unit, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-emerald-700">Rp {{ number_format($sale->total_price, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $sale->buyer ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('sales.receipt', $sale) }}" target="_blank"
                                               class="inline-flex items-center px-2.5 py-1.5 bg-emerald-100 hover:bg-emerald-200 text-emerald-700 font-bold text-xs rounded-md transition mr-1">
                                               🧾 Struk
                                            </a>
                                            <a href="{{ route('sales.edit', $sale) }}"
                                               class="inline-flex items-center px-2.5 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold text-xs rounded-md transition mr-1">
                                               ✏️ Edit
                                            </a>
                                            <form action="{{ route('sales.destroy', $sale) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan penjualan ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-2.5 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-xs rounded-md transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="px-6 py-10 text-sm text-slate-400 text-center font-medium">Belum ada transaksi penjualan dicatat.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    </form>

                    <div class="mt-4">
                        {{ $sales->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== FLOATING ACTION BAR (muncul di HP saat ada yang dicentang) ===== --}}
    <div id="fab-bar"
         style="display:none;"
         class="fixed bottom-0 left-0 right-0 z-50 flex items-center justify-between gap-3 px-4 py-3 bg-indigo-700 text-white shadow-2xl"
         role="region" aria-label="Aksi Pilihan">

        {{-- Info jumlah dipilih --}}
        <div class="flex items-center gap-2 text-sm font-semibold">
            <span id="fab-count-badge"
                  class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-white text-indigo-700 font-extrabold text-sm">
                0
            </span>
            <span id="fab-label">transaksi dipilih</span>
        </div>

        {{-- Tombol cetak — submit form yang sama --}}
        <button type="submit"
                form="batch-receipt-form"
                id="fab-print-btn"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 font-bold text-sm rounded-full shadow-lg active:scale-95 transition-transform select-none">
            🖨️ Cetak Nota
        </button>
    </div>

    <script>
        /* ── Pilih Semua ───────────────────────────────────── */
        function toggleSelectAll(source) {
            document.querySelectorAll('.sale-checkbox')
                    .forEach(cb => cb.checked = source.checked);
            syncUI();
        }

        /* ── Pasang event ke setiap checkbox ──────────────── */
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.sale-checkbox').forEach(cb => {
                cb.addEventListener('change', function () {
                    syncMasterCheckbox();
                    syncUI();
                });
            });
        });

        /* ── Update master checkbox (√ / indeterminate / □) ── */
        function syncMasterCheckbox() {
            const all     = document.querySelectorAll('.sale-checkbox');
            const checked = document.querySelectorAll('.sale-checkbox:checked');
            const master  = document.getElementById('select-all-sales');
            if (!master) return;
            master.checked       = (all.length > 0 && all.length === checked.length);
            master.indeterminate = (checked.length > 0 && checked.length < all.length);
        }

        /* ── Update semua UI berdasarkan jumlah yang dipilih ─ */
        function syncUI() {
            const count = document.querySelectorAll('.sale-checkbox:checked').length;

            /* Tombol header (desktop) */
            const headerBtn = document.querySelector('button[form="batch-receipt-form"]:not(#fab-print-btn)');
            if (headerBtn) {
                headerBtn.textContent = count > 0
                    ? `📄 Cetak Nota Gabungan (${count} dipilih)`
                    : '📄 Cetak Nota Gabungan';
                headerBtn.disabled = (count === 0);
                headerBtn.classList.toggle('opacity-50', count === 0);
                headerBtn.classList.toggle('cursor-not-allowed', count === 0);
            }

            /* Floating bar (mobile / semua layar) */
            const fab       = document.getElementById('fab-bar');
            const badge     = document.getElementById('fab-count-badge');
            const fabLabel  = document.getElementById('fab-label');
            if (fab) {
                if (count > 0) {
                    fab.style.display = 'flex';
                    badge.textContent = count;
                    fabLabel.textContent = count === 1 ? 'transaksi dipilih' : 'transaksi dipilih';
                } else {
                    fab.style.display = 'none';
                }
            }
        }
    </script>
</x-app-layout>
