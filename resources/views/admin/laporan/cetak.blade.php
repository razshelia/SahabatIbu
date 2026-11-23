<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Operasional Bank Sampah Sahabat Ibu</title>
    <style>
        body { font-family: sans-serif; font-size: 11px; color: #111; margin: 20px; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .header h1 { font-size: 16px; margin: 0 0 5px 0; }
        .header p { margin: 0; font-size: 11px; color: #555; }
        .meta { margin-bottom: 15px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 5px 8px; text-align: left; }
        th { background-color: #f5f5f5; font-size: 10px; text-transform: uppercase; }
        .text-right { text-align: right; }
        .summary-box { display: flex; gap: 15px; margin-bottom: 20px; }
        .summary-card { border: 1px solid #ccc; padding: 10px; flex: 1; }
        .summary-card h4 { margin: 0 0 5px 0; font-size: 10px; text-transform: uppercase; color: #666; }
        .summary-card p { margin: 0; font-size: 14px; font-weight: bold; }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print" style="margin-bottom: 15px; text-align: right;">
        <button onclick="window.print()" style="padding: 6px 12px; background: #047857; color: white; border: none; cursor: pointer; border-radius: 4px;">
            Cetak Dokumen Ini
        </button>
    </div>

    <div class="header">
        <h1>BANK SAMPAH SAHABAT IBU</h1>
        <p>Perumahan Taman Gading, Kelurahan Tegal Besar, Kecamatan Kaliwates, Kabupaten Jember</p>
        <p><strong>Laporan Rekapitulasi Operasional & Keuangan</strong></p>
    </div>

    <div class="meta">
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d F Y') }}<br>
        <strong>Dicetak pada:</strong> {{ now()->format('d/m/Y H:i') }}
    </div>

    <div class="summary-box">
        <div class="summary-card">
            <h4>Total Tabungan Masuk</h4>
            <p>Rp {{ number_format($rekap['total_setoran'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h4>Total Sampah Terkumpul</h4>
            <p>{{ number_format($rekap['total_berat'], 2, ',', '.') }} kg</p>
        </div>
        <div class="summary-card">
            <h4>Total Penarikan Dicairkan</h4>
            <p>Rp {{ number_format($rekap['total_penarikan'], 0, ',', '.') }}</p>
        </div>
        <div class="summary-card">
            <h4>Selisih Kas Bersih</h4>
            <p>Rp {{ number_format($rekap['saldo_bersih'], 0, ',', '.') }}</p>
        </div>
    </div>

    <h3>1. Rekapitulasi Sampah Berdasarkan Jenis</h3>
    <table>
        <thead>
            <tr>
                <th>Jenis Sampah</th>
                <th class="text-right">Total Berat</th>
                <th class="text-right">Total Nilai Tabungan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rekapJenis as $j)
                <tr>
                    <td>{{ $j->jenis_nama }}</td>
                    <td class="text-right">{{ number_format($j->total_berat, 2, ',', '.') }} {{ $j->jenis_satuan }}</td>
                    <td class="text-right">Rp {{ number_format($j->total_nilai, 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>2. Riwayat Transaksi Setoran Sampah (Berhasil)</h3>
    <table>
        <thead>
            <tr>
                <th>No. Nasabah</th>
                <th>Nama Nasabah</th>
                <th>Kode</th>
                <th>Tanggal</th>
                <th class="text-right">Total Berat</th>
                <th class="text-right">Nilai Tabungan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($setoranList as $s)
                <tr>
                    <td>{{ $s->nasabah->nomor_nasabah }}</td>
                    <td>{{ $s->nasabah->user->name }}</td>
                    <td>{{ $s->kode }}</td>
                    <td>{{ $s->tanggal->format('d/m/Y H:i') }}</td>
                    <td class="text-right">{{ number_format($s->total_berat, 2, ',', '.') }} kg</td>
                    <td class="text-right">Rp {{ number_format($s->total_nilai, 0, ',', '.') }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align: center; color: #999;">Tidak ada data setoran.</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3>3. Riwayat Penarikan Dana (Dicairkan)</h3>
    <table>
        <thead>
            <tr>
                <th>No. Nasabah</th>
                <th>Nama Nasabah</th>
                <th>Kode</th>
                <th>Tanggal Dicairkan</th>
                <th>Metode</th>
                <th class="text-right">Nominal (Rp)</th>
                <th>Ref / Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($penarikanList as $p)
                <tr>
                    <td>{{ $p->nasabah->nomor_nasabah }}</td>
                    <td>{{ $p->nasabah->user->name }}</td>
                    <td>{{ $p->kode }}</td>
                    <td>{{ $p->dicairkan_pada?->format('d/m/Y H:i') }}</td>
                    <td>{{ ucfirst($p->metode ?? 'tunai') }}</td>
                    <td class="text-right">Rp {{ number_format($p->nominal, 0, ',', '.') }}</td>
                    <td>{{ $p->catatan_referensi ?? '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="7" style="text-align: center; color: #999;">Tidak ada data penarikan.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
