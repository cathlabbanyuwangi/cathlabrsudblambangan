<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Cetak Laporan Kustom - Cathlab RSUD Blambangan</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #000;
            line-height: 1.3;
            margin: 0;
            padding: 0;
            background: #fff;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
        }
        .header h2 { margin: 0; font-size: 14px; font-weight: bold; }
        .header p { margin: 3px 0 0 0; font-size: 11px; }

        .filter-info {
            margin-bottom: 15px;
            font-size: 10.5px;
            background: #f8fafc;
            border: 1px solid #cbd5e1;
            padding: 8px 12px;
            border-radius: 4px;
        }
        .filter-info ul {
            margin: 4px 0 0 15px;
            padding: 0;
        }

        .table-data {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .table-data th, .table-data td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: left;
            font-size: 10px;
        }
        .table-data th {
            background-color: #e2e8f0 !important;
            text-align: center;
            font-weight: bold;
            text-transform: uppercase;
        }

        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn-print {
            padding: 8px 16px;
            background: #000;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-weight: bold;
        }

        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Cetak Dokumen</button>
    </div>

    <div class="header">
        <h2>LAPORAN REKAPITULASI & TINDAKAN KUSTOM</h2>
        <p>INSTALASI KARDIOVASKULAR / CATHLAB - RSUD BLAMBANGAN</p>
    </div>

    <!-- Informasi Filter yang Dipilih -->
    <div class="filter-info">
        <strong>Parameter Filter Dipilih:</strong>
        <ul>
            <li>Periode Bulan: {{ $startMonth ?? 'Semua' }} s/d {{ $endMonth ?? 'Semua' }}</li>
            <li>Divisi ID: {{ !empty($selectedDivisions) ? implode(', ', $selectedDivisions) : 'Semua Divisi' }}</li>
            <li>Tindakan ID: {{ !empty($selectedActions) ? implode(', ', $selectedActions) : 'Semua Tindakan' }}</li>
        </ul>
    </div>

    <!-- Tabel Hasil Laporan dengan Tambahan Kolom Tanggal Lahir -->
    <table class="table-data">
        <thead>
            <tr>
                <th width="6%">No</th>
                <th width="18%">Tanggal / Waktu</th>
                <th width="26%">Nama Pasien</th>
                <th width="16%">No. Rekam Medis</th>
                <th width="16%">Tanggal Lahir</th>
                <th width="18%">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports ?? [] as $index => $row)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>{{ $row->created_at ?? '-' }}</td>
                <td>{{ $row->patient->name ?? '-' }}</td>
                <td>{{ $row->patient->medical_record_number ?? '-' }}</td>
                <td style="text-align: center;">
                    {{ optional($row->patient)->date_of_birth ? \Carbon\Carbon::parse($row->patient->date_of_birth)->format('d-m-Y') : '-' }}
                </td>
                <td>{{ $row->status ?? '-' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; font-style: italic; color: #555;">
                    Tidak ada data yang ditemukan sesuai filter yang dipilih.
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>