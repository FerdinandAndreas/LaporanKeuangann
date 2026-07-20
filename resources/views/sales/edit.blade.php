<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Transaksi Penjualan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900" x-data="saleForm()">
                    <form method="POST" action="{{ route('sales.update', $sale) }}" class="space-y-6">
                        @csrf
                        @method('PATCH')

                        <!-- Product Selection -->
                        <div>
                            <x-input-label for="product_id" :value="__('Pilih Produk (Master Data - Opsional)')" />
                            <select id="product_id" name="product_id" x-model="productId" @change="onProductChange()" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="">-- Ketik Manual / Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-name="{{ $product->name }}" data-unit="{{ $product->unit }}">{{ $product->name }} ({{ $product->unit }})</option>
                                @endforeach
                            </select>
                            <x-input-error class="mt-2" :messages="$errors->get('product_id')" />
                        </div>

                        <!-- Item Name -->
                        <div>
                            <x-input-label for="item_name" :value="__('Nama Barang')" />
                            <x-text-input id="item_name" name="item_name" type="text" class="mt-1 block w-full" x-model="itemName" ::disabled="productId !== ''" required />
                            <span class="text-xs text-gray-400" x-show="productId !== ''">Nama barang disesuaikan dengan master produk terpilih.</span>
                            <x-input-error class="mt-2" :messages="$errors->get('item_name')" />
                        </div>

                        <!-- Quantity -->
                        <div>
                            <x-input-label for="quantity" :value="__('Jumlah (Quantity)')" />
                            <x-text-input id="quantity" name="quantity" type="number" step="0.01" class="mt-1 block w-full" x-model.number="quantity" @input="calculateTotal()" required />
                            <x-input-error class="mt-2" :messages="$errors->get('quantity')" />
                        </div>

                        <!-- Unit -->
                        <div>
                            <x-input-label for="unit" :value="__('Satuan')" />
                            <x-text-input id="unit" name="unit" type="text" class="mt-1 block w-full" x-model="unitName" ::disabled="productId !== ''" required />
                            <span class="text-xs text-gray-400" x-show="productId !== ''">Satuan disesuaikan dengan master produk terpilih.</span>
                            <x-input-error class="mt-2" :messages="$errors->get('unit')" />
                        </div>

                        <!-- Price Per Unit -->
                        <div>
                            <x-input-label for="price_per_unit" :value="__('Harga Per Satuan (Rp)')" />
                            <x-text-input id="price_per_unit" name="price_per_unit" type="number" step="0.01" class="mt-1 block w-full" x-model.number="pricePerUnit" @input="calculateTotal()" required />
                            <x-input-error class="mt-2" :messages="$errors->get('price_per_unit')" />
                        </div>

                        <!-- Total Price Preview -->
                        <div>
                            <label class="block font-medium text-sm text-gray-700">Total Pemasukan (Otomatis)</label>
                            <div class="mt-2 p-3 bg-gray-100 rounded-md font-bold text-lg text-gray-800">
                                Rp <span x-text="formatCurrency(totalPrice)">0,00</span>
                            </div>
                        </div>

                        <!-- Buyer -->
                        <div>
                            <x-input-label for="buyer" :value="__('Nama Pembeli (Opsional)')" />
                            <x-text-input id="buyer" name="buyer" type="text" class="mt-1 block w-full" :value="old('buyer', $sale->buyer)" />
                            <x-input-error class="mt-2" :messages="$errors->get('buyer')" />
                        </div>

                        <!-- Sale Date -->
                        <div>
                            <x-input-label for="sale_date" :value="__('Tanggal Penjualan')" />
                            <x-text-input id="sale_date" name="sale_date" type="date" class="mt-1 block w-full" :value="old('sale_date', $sale->sale_date->toDateString())" required />
                            <x-input-error class="mt-2" :messages="$errors->get('sale_date')" />
                        </div>

                        <!-- Notes -->
                        <div>
                            <x-input-label for="notes" :value="__('Catatan (Opsional)')" />
                            <textarea id="notes" name="notes" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" rows="3">{{ old('notes', $sale->notes) }}</textarea>
                            <x-input-error class="mt-2" :messages="$errors->get('notes')" />
                        </div>

                        <!-- Fields that should be sent even if disabled by x-model/disabled attribute -->
                        <template x-if="productId !== ''">
                            <div>
                                <input type="hidden" name="item_name" :value="itemName">
                                <input type="hidden" name="unit" :value="unitName">
                            </div>
                        </template>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Perbarui') }}</x-primary-button>
                            <a href="{{ route('sales.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function saleForm() {
            return {
                productId: '{{ old('product_id', $sale->product_id ?? '') }}',
                itemName: '{{ old('item_name', $sale->item_name) }}',
                quantity: {{ old('quantity', $sale->quantity) }},
                unitName: '{{ old('unit', $sale->unit) }}',
                pricePerUnit: {{ old('price_per_unit', $sale->price_per_unit) }},
                totalPrice: 0,
                init() {
                    this.calculateTotal();
                    if(this.productId !== '') {
                        this.onProductChange();
                    }
                },
                onProductChange() {
                    if (this.productId === '') {
                        this.itemName = '{{ $sale->item_name }}';
                        this.unitName = '{{ $sale->unit }}';
                        return;
                    }
                    const select = document.getElementById('product_id');
                    const selectedOption = select.options[select.selectedIndex];
                    this.itemName = selectedOption.getAttribute('data-name');
                    this.unitName = selectedOption.getAttribute('data-unit');
                },
                calculateTotal() {
                    this.totalPrice = this.quantity * this.pricePerUnit;
                },
                formatCurrency(value) {
                    if (isNaN(value)) return '0,00';
                    return new Intl.NumberFormat('id-ID', { minimumFractionDigits: 2 }).format(value);
                }
            }
        }
    </script>
</x-app-layout>
