<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nota Penjualan #{{ $sale->id }}</title>
    <style>
        :root {
            --invoice-blue: #0b3fa8;
            --border-blue: #5b8df5;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
            margin: 0;
            padding: 40px 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Invoice Container */
        .invoice-card {
            background-color: #ffffff;
            width: 100%;
            max-width: 650px;
            padding: 40px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            border-radius: 4px;
            position: relative;
            box-sizing: border-box;
        }

        /* Header Style */
        .title {
            color: var(--invoice-blue);
            font-size: 28px;
            font-weight: 800;
            margin: 0 0 10px 0;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-line-thick {
            height: 3px;
            background-color: var(--invoice-blue);
            border: none;
            margin: 0 0 25px 0;
        }

        .customer-section {
            margin-bottom: 25px;
        }

        .customer-label {
            font-size: 15px;
            color: var(--invoice-blue);
            font-weight: 600;
        }

        .customer-name-underline {
            display: inline-block;
            min-width: 250px;
            border-bottom: 1.5px solid var(--invoice-blue);
            padding-bottom: 2px;
            font-size: 16px;
            color: #1e293b;
            font-weight: 700;
            margin-left: 10px;
        }

        .header-line-medium {
            height: 3px;
            background-color: var(--invoice-blue);
            border: none;
            margin: 10px 0 35px 0;
        }

        /* Table Style */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        .items-table th {
            border: 1px solid var(--border-blue);
            color: var(--invoice-blue);
            font-weight: 800;
            font-size: 12px;
            text-transform: uppercase;
            padding: 10px;
            text-align: left;
            letter-spacing: 0.5px;
        }

        .items-table td {
            border: 1px solid var(--border-blue);
            padding: 10px;
            font-size: 14px;
            height: 20px; /* Force rows to have height similar to blank rows */
            color: #1e293b;
        }

        /* Column Widths */
        .col-qty { width: 18%; }
        .col-name { width: 42%; }
        .col-price { width: 20%; }
        .col-total { width: 20%; }

        .text-right { text-align: right; }
        .text-center { text-align: center; }

        /* Total Section */
        .total-container {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            margin-bottom: 40px;
            font-size: 16px;
        }

        .total-label {
            color: var(--invoice-blue);
            font-weight: 800;
            margin-right: 15px;
        }

        .total-value-underline {
            font-size: 16px;
            font-weight: 800;
            color: var(--invoice-blue);
            border-bottom: 1.5px solid var(--invoice-blue);
            padding-bottom: 2px;
            min-width: 180px;
            text-align: right;
            display: inline-block;
        }

        /* Signature Section */
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 30px;
        }

        .signature-box {
            text-align: center;
            width: 200px;
        }

        .signature-title {
            color: var(--invoice-blue);
            font-weight: 800;
            font-size: 14px;
            margin-bottom: 60px;
        }

        .signature-line {
            border-bottom: 1.5px solid var(--invoice-blue);
            height: 1px;
            width: 100%;
        }

        /* Action Buttons */
        .actions-bar {
            margin-bottom: 20px;
            display: flex;
            gap: 12px;
        }

        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-weight: 700;
            font-size: 14px;
            cursor: pointer;
            border: none;
            transition: all 0.15s ease-in-out;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .btn-indigo {
            background-color: #4f46e5;
            color: white;
        }

        .btn-indigo:hover {
            background-color: #4338ca;
        }

        .btn-emerald {
            background-color: #10b981;
            color: white;
        }

        .btn-emerald:hover {
            background-color: #059669;
        }

        .btn-gray {
            background-color: #6b7280;
            color: white;
        }

        .btn-gray:hover {
            background-color: #55585d;
        }

        /* Print Media Styles */
        @media print {
            body {
                background-color: #ffffff;
                padding: 0;
            }
            .invoice-card {
                box-shadow: none;
                padding: 20px;
                max-width: 100%;
            }
            .actions-bar {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Actions Bar -->
    <div class="actions-bar">
        <button onclick="window.print()" class="btn btn-indigo">🖨️ Cetak Nota</button>
        <button onclick="downloadPNG()" class="btn btn-emerald">📥 Unduh Gambar (PNG)</button>
        <a href="{{ route('sales.index') }}" class="btn btn-gray">Kembali</a>
    </div>

    <!-- Invoice Sheet to capture -->
    <div class="invoice-card" id="invoice-sheet">
        <h1 class="title">Transaksi Penjualan</h1>
        <hr class="header-line-thick">

        <div class="customer-section">
            <span class="customer-label">Tuan Toko</span>
            <span class="customer-name-underline">{{ $sale->buyer ?: 'Umum' }}</span>
        </div>

        <hr class="header-line-medium">

        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-qty text-center">Banyaknya</th>
                    <th class="col-name">Nama Barang</th>
                    <th class="col-price text-right">Harga</th>
                    <th class="col-total text-right">Jumlah</th>
                </tr>
            </thead>
            <tbody>
                <!-- Active Row: The sale transaction item -->
                <tr>
                    <td class="text-center font-semibold">
                        {{ number_format($sale->quantity, 2, ',', '.') }} {{ $sale->unit }}
                    </td>
                    <td class="font-semibold">
                        {{ $sale->item_name }}
                    </td>
                    <td class="text-right">
                        Rp {{ number_format($sale->price_per_unit, 0, ',', '.') }}
                    </td>
                    <td class="text-right font-bold">
                        Rp {{ number_format($sale->total_price, 0, ',', '.') }}
                    </td>
                </tr>

                <!-- 8 Blank Rows matching the physical template layout -->
                @for($i = 0; $i < 8; $i++)
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                @endfor
            </tbody>
        </table>

        <!-- Total Price Footer -->
        <div class="total-container">
            <span class="total-label">Jumlah Rp.</span>
            <span class="total-value-underline">
                {{ number_format($sale->total_price, 0, ',', '.') }}
            </span>
        </div>

        <!-- Signature Section -->
        <div class="signatures">
            <div class="signature-box">
                <div class="signature-title">Yang Menerima</div>
                <div class="signature-line"></div>
            </div>
            <div class="signature-box">
                <div class="signature-title">Hormat Kami</div>
                <div class="signature-line"></div>
            </div>
        </div>
    </div>

    <!-- html2canvas client-side library for high-resolution PNG export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script>
        function downloadPNG() {
            const sheet = document.getElementById('invoice-sheet');
            const options = {
                scale: 3, // scale up by 3 for sharp print-quality image
                useCORS: true,
                backgroundColor: '#ffffff'
            };
            
            html2canvas(sheet, options).then(canvas => {
                const link = document.createElement('a');
                link.download = 'Nota_Penjualan_{{ $sale->id }}_{{ Str::slug($sale->buyer ?: "Umum") }}.png';
                link.href = canvas.toDataURL('image/png');
                link.click();
            });
        }
    </script>
</body>
</html>
