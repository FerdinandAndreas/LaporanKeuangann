<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            🛒 Transaksi Pembelian (Stok Masuk)
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-800 rounded-r-lg font-medium">
                    ✅ {{ session('success') }}
                </div>
            @endif

            <!-- Summary Card -->
            <div class="bg-rose-700 rounded-xl shadow-md text-white mb-6 p-6 flex flex-wrap justify-between items-center gap-4">
                <div>
                    <p class="text-xs font-bold uppercase tracking-widest text-rose-200">Total Pengeluaran Pembelian</p>
                    <p class="text-3xl font-extrabold mt-1">Rp {{ number_format($totalPurchases, 2, ',', '.') }}</p>
                </div>
                <a href="{{ route('purchases.create') }}"
                   class="inline-flex items-center gap-1.5 px-4 py-2.5 bg-white text-rose-700 font-bold text-sm rounded-lg shadow hover:bg-rose-50 transition">
                    🛒 + Catat Pembelian
                </a>
            </div>

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6">
                    <div class="flex justify-between items-center mb-5">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">🗂️ Riwayat Pembelian Barang</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Semua transaksi pembelian / stok masuk.</p>
                        </div>
                    </div>

                    <!-- Search & Filter -->
                    <form method="GET" action="{{ route('purchases.index') }}"
                          class="mb-6 grid grid-cols-1 sm:grid-cols-4 gap-4 items-end bg-slate-50 p-4 rounded-lg border border-slate-200">
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Cari Nama Barang</label>
                            <input type="text" name="search" value="{{ request('search') }}" placeholder="Misal: Beras IR64"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Supplier</label>
                            <input type="text" name="supplier" value="{{ request('supplier') }}" placeholder="Nama supplier"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Dari Tanggal</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}"
                                   class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                        </div>
                        <div class="flex gap-2">
                            <div class="flex-1">
                                <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1">Sampai Tanggal</label>
                                <input type="date" name="end_date" value="{{ request('end_date') }}"
                                       class="block w-full border-2 border-slate-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg text-sm px-3 py-2">
                            </div>
                            <button type="submit"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-bold transition self-end shadow">
                                Cari
                            </button>
                            <a href="{{ route('purchases.index') }}"
                               class="px-3 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 rounded-lg text-sm font-bold transition self-end">
                                Reset
                            </a>
                        </div>
                    </form>

                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Tanggal</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Nama Barang</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Jumlah (Qty)</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Harga Satuan</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Total Harga</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Supplier</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($purchases as $purchase)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-700">{{ $purchase->purchase_date->format('d-m-Y') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">
                                            {{ $purchase->item_name }}
                                            @if($purchase->product)
                                                <span class="text-xs font-normal text-slate-400">({{ $purchase->product->name }})</span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ number_format($purchase->quantity, 2, ',', '.') }} {{ $purchase->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">Rp {{ number_format($purchase->price_per_unit, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-extrabold text-rose-700">Rp {{ number_format($purchase->total_price, 2, ',', '.') }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">{{ $purchase->supplier ?? '—' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('purchases.edit', $purchase) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold text-xs rounded-md transition mr-1">
                                               ✏️ Edit
                                            </a>
                                            <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus catatan pembelian ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                        class="inline-flex items-center px-3 py-1.5 bg-rose-100 hover:bg-rose-200 text-rose-700 font-bold text-xs rounded-md transition">
                                                    🗑️ Hapus
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-10 text-sm text-slate-400 text-center font-medium">
                                            Belum ada transaksi pembelian dicatat.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $purchases->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
