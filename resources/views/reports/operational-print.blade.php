<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Efisiensi Operasional & Flow Cathlab</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 11px; color: #000; line-height: 1.3; margin: 20px; }
        .header { text-align: center; font-weight: bold; margin-bottom: 20px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 15px; }
        .header p { margin: 4px 0 0 0; font-size: 11px; }
        .table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        .table th, .table td { border: 1px solid #000; padding: 6px 8px; text-align: left; }
        .table th { background: #f2f2f2; font-size: 10px; text-transform: uppercase; text-align: center; }
        .footer { width: 100%; margin-top: 30px; page-break-inside: avoid; }
        .footer td { text-align: center; height: 60px; vertical-align: bottom; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>LAPORAN AUDIT KEPATUHAN WAKTU & OUTCOME KLINIS (STEMI)</h2>
        <p>INSTALASI KARTIOVASKULAR / CATHLAB - RSUD BLAMBANGAN</p>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="25%">Nama Pasien & No. RM</th>
                <th width="15%">Waktu Tiba (Door)</th>
                <th width="15%">Waktu Tindakan (Balloon)</th>
                <th width="12%">Durasi Total</th>
                <th width="13%">Kepatuhan</th>
                <th width="15%">Outcome</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $index => $log)
            <tr>
                <td style="text-align: center;">{{ $index + 1 }}</td>
                <td>
                    <strong>{{ $log->patient_name }}</strong><br>
                    <span style="font-size: 10px; color: #555;">RM: {{ $log->medical_record_number }}</span>
                </td>
                <td>{{ $log->arrived_hospital_at ?? '-' }}</td>
                <td>{{ $log->balloon_inflation_at ?? '-' }}</td>
                <td style="font-weight: bold;">{{ isset($log->duration_minutes) ? $log->duration_minutes . ' Menit' : '-' }}</td>
                <td>
                    @if(isset($log->duration_minutes))
                        {{ $log->duration_minutes <= 90 ? 'Sesuai (< 90m)' : 'Terlambat (> 90m)' }}
                    @else
                        -
                    @endif
                </td>
                <td>{{ isset($log->is_successful) && $log->is_successful ? 'Berhasil (TIMI 3)' : 'Evaluasi' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7" style="text-align: center; font-style: italic; padding: 15px;">Belum ada data log kasus CITO / STEMI yang tercatat.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td width="70%"></td>
            <td width="30%">
                Banyuwangi, {{ now()->format('d F Y') }}<br>
                Penanggung Jawab / Kepala Instalasi Cathlab<br><br><br><br>
                <strong><u>( ................................................. )</u></strong>
            </td>
        </tr>
    </table>
</body>
</html>