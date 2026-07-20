<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Toko Beras</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: #333;
            line-height: 1.4;
            margin: 0;
            padding: 30px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .header h1 {
            margin: 0;
            font-size: 20px;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12px;
            color: #666;
        }
        .meta-info {
            margin-bottom: 20px;
        }
        .meta-info table {
            width: 100%;
        }
        .meta-info td {
            padding: 3px 0;
        }
        .section-title {
            font-size: 14px;
            font-weight: bold;
            border-bottom: 1px solid #ccc;
            padding-bottom: 5px;
            margin-top: 30px;
            margin-bottom: 10px;
            text-transform: uppercase;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table.data-table th {
            background-color: #f5f5f5;
            font-weight: bold;
        }
        .text-right {
            text-align: right !important;
        }
        .text-center {
            text-align: center !important;
        }
        .summary-box {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 15px;
            margin-top: 20px;
            border-radius: 5px;
        }
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }
        .summary-item {
            padding: 5px 0;
        }
        .summary-item span {
            font-weight: bold;
        }
        .profit-positive {
            color: green;
        }
        .profit-negative {
            color: red;
        }
        .footer {
            margin-top: 50px;
            text-align: right;
            font-style: italic;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .page-break {
                page-break-before: always;
            }
        }
        .btn-print {
            background-color: #4F46E5;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 20px;
            display: inline-block;
            text-decoration: none;
        }
        .btn-print:hover {
            background-color: #4338CA;
        }
    </style>
</head>
<body>
    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" class="btn-print">Cetak Laporan / Simpan PDF</button>
        <button onclick="window.close()" class="btn-print" style="background-color: #ef4444; margin-left: 10px;">Tutup Halaman</button>
    </div>

    <div class="header">
        <h1>Laporan Keuangan Toko Beras</h1>
        <p>Sistem Informasi Keuangan & Stok</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 15%;"><strong>Periode Laporan</strong></td>
                <td>: {{ $startDate ? date('d-m-Y', strtotime($startDate)) : 'Awal Catatan' }} s/d {{ $endDate ? date('d-m-Y', strtotime($endDate)) : 'Hari Ini' }}</td>
                <td style="width: 25%; text-align: right;"><strong>Tanggal Cetak</strong>: {{ date('d-m-Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="summary-box">
        <h3 style="margin-top: 0; text-transform: uppercase; border-bottom: 1px solid #ddd; padding-bottom: 5px;">Ringkasan Finansial</h3>
        <div class="summary-grid">
            <div class="summary-item">Total Modal Masuk: <span>Rp {{ number_format($totalCapital, 2, ',', '.') }}</span></div>
            <div class="summary-item">Total Pembelian (HPP): <span>Rp {{ number_format($totalPurchases, 2, ',', '.') }}</span></div>
            <div class="summary-item">Total Penjualan (Omzet): <span>Rp {{ number_format($totalSales, 2, ',', '.') }}</span></div>
            <div class="summary-item">Laba / Rugi Kotor: 
                <span class="{{ $profit >= 0 ? 'profit-positive' : 'profit-negative' }}">
                    {{ $profit >= 0 ? '+' : '' }}Rp {{ number_format($profit, 2, ',', '.') }}
                </span>
            </div>
        </div>
    </div>

    <div class="section-title">Data Modal</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th style="width: 20%;">Tipe</th>
                <th>Jumlah Modal</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($capitals as $c)
                <tr>
                    <td>{{ $c->date->format('d-m-Y') }}</td>
                    <td>{{ $c->type == 'awal' ? 'Modal Awal' : 'Modal Tambahan' }}</td>
                    <td class="text-right">Rp {{ number_format($c->amount, 2, ',', '.') }}</td>
                    <td>{{ $c->description ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="text-center">Tidak ada data modal pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="page-break"></div>

    <div class="section-title" style="margin-top: 0;">Data Pembelian (Stok Masuk)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th>Nama Barang</th>
                <th style="width: 12%;">Jumlah</th>
                <th style="width: 15%;">Harga Satuan</th>
                <th style="width: 18%;">Total Harga</th>
                <th>Supplier</th>
            </tr>
        </thead>
        <tbody>
            @forelse($purchases as $p)
                <tr>
                    <td>{{ $p->purchase_date->format('d-m-Y') }}</td>
                    <td>{{ $p->item_name }}</td>
                    <td>{{ number_format($p->quantity, 2, ',', '.') }} {{ $p->unit }}</td>
                    <td class="text-right">Rp {{ number_format($p->price_per_unit, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($p->total_price, 2, ',', '.') }}</td>
                    <td>{{ $p->supplier ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data pembelian pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="section-title">Data Penjualan (Stok Keluar)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 15%;">Tanggal</th>
                <th>Nama Barang</th>
                <th style="width: 12%;">Jumlah</th>
                <th style="width: 15%;">Harga Satuan</th>
                <th style="width: 18%;">Total Harga</th>
                <th>Pembeli</th>
            </tr>
        </thead>
        <tbody>
            @forelse($sales as $s)
                <tr>
                    <td>{{ $s->sale_date->format('d-m-Y') }}</td>
                    <td>{{ $s->item_name }}</td>
                    <td>{{ number_format($s->quantity, 2, ',', '.') }} {{ $s->unit }}</td>
                    <td class="text-right">Rp {{ number_format($s->price_per_unit, 2, ',', '.') }}</td>
                    <td class="text-right">Rp {{ number_format($s->total_price, 2, ',', '.') }}</td>
                    <td>{{ $s->buyer ?: '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data penjualan pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak otomatis oleh Sistem Laporan Keuangan Toko Beras.
    </div>

    <script>
        // Auto trigger print dialogue on page load if opened via print action
        window.onload = function() {
            // Uncomment if you want to open print automatically
            // window.print();
        };
    </script>
</body>
</html>
