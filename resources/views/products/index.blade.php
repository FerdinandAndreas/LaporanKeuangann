<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            📦 Daftar Produk
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

            <div class="bg-white overflow-hidden shadow-sm rounded-xl border border-slate-200">
                <div class="p-6">
                    <!-- Header bar -->
                    <div class="flex justify-between items-center mb-6">
                        <div>
                            <h3 class="text-base font-bold text-slate-800">📦 Master Data Produk Beras</h3>
                            <p class="text-xs text-slate-500 mt-0.5">Kelola produk dan pantau stok secara otomatis.</p>
                        </div>
                        <a href="{{ route('products.create') }}"
                           class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-sm rounded-lg shadow transition">
                            + Tambah Produk
                        </a>
                    </div>

                    <div class="overflow-x-auto rounded-lg border border-slate-200">
                        <table class="min-w-full divide-y divide-slate-200">
                            <thead class="bg-slate-800">
                                <tr>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Nama Produk</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Satuan Unit</th>
                                    <th scope="col" class="px-6 py-3 text-left text-xs font-bold text-slate-300 uppercase tracking-wider">Stok Saat Ini</th>
                                    <th scope="col" class="px-6 py-3 text-right text-xs font-bold text-slate-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-slate-100">
                                @forelse($products as $product)
                                    <tr class="hover:bg-slate-50 transition-colors">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-slate-800">{{ $product->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">{{ $product->unit }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            @php $stock = (float)$product->current_stock; @endphp
                                            @if($stock < 10)
                                                <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-rose-600 text-white">
                                                    🔴 Kritis ({{ number_format($stock, 2, ',', '.') }} {{ $product->unit }})
                                                </span>
                                            @elseif($stock <= 50)
                                                <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-amber-500 text-white">
                                                    🟡 Terbatas ({{ number_format($stock, 2, ',', '.') }} {{ $product->unit }})
                                                </span>
                                            @else
                                                <span class="px-2.5 py-1 inline-flex text-xs font-bold rounded-full bg-emerald-600 text-white">
                                                    🟢 Aman ({{ number_format($stock, 2, ',', '.') }} {{ $product->unit }})
                                                </span>
                                            @endif
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('products.edit', $product) }}"
                                               class="inline-flex items-center px-3 py-1.5 bg-indigo-100 hover:bg-indigo-200 text-indigo-700 font-bold text-xs rounded-md transition mr-2">
                                               ✏️ Edit
                                            </a>
                                            <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline"
                                                  onsubmit="return confirm('Apakah Anda yakin ingin menghapus produk ini?');">
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
                                        <td colspan="4" class="px-6 py-10 text-sm text-slate-400 text-center font-medium">
                                            Belum ada produk. Silakan tambahkan produk baru.
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
</x-app-layout>
