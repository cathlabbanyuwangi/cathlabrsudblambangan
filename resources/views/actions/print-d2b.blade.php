<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Lembar Audit Door to Balloon - {{ $action->patient->name ?? 'Pasien' }}</title>
    <style>
        /* Perbesar margin halaman agar tidak mepet */
        @page {
            size: A4 portrait;
            margin: 20mm 15mm;
        }
        
        body { 
            font-family: Arial, Helvetica, sans-serif; 
            font-size: 11px; 
            color: #000; 
            line-height: 1.35; 
            margin: 0; 
            padding: 0;
            background: #fff;
        }

        /* Container utama dengan padding kiri-kanan yang lega */
        .page-container {
            padding: 0 10px;
        }
        
        /* Kop Laporan / Header */
        .header { 
            text-align: center; 
            margin-bottom: 15px; 
            border-bottom: 3px double #000; 
            padding-bottom: 8px; 
        }
        .header h2 { 
            margin: 0; 
            font-size: 15px; 
            font-weight: bold; 
            letter-spacing: 0.5px;
        }
        .header p { 
            margin: 4px 0 0 0; 
            font-size: 11px; 
            font-weight: bold; 
            text-transform: uppercase; 
            color: #333;
        }

        /* Kotak Informasi Pasien */
        .info-card {
            border: 1.5px solid #000;
            padding: 10px 12px;
            margin-bottom: 15px;
            background: #fafafa;
        }
        .info-table { width: 100%; border-collapse: collapse; font-size: 11px; }
        .info-table td { padding: 3px 5px; vertical-align: top; }

        /* Tabel Audit Utama */
        .audit-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .audit-table th, .audit-table td { 
            border: 1px solid #000; 
            padding: 5px 7px; 
            text-align: left; 
            font-size: 10.5px; 
        }
        .audit-table th { 
            background: #e2e8f0 !important; 
            text-align: center; 
            font-weight: bold; 
            text-transform: uppercase; 
            letter-spacing: 0.3px;
        }
        .audit-table tr:nth-child(even) {
            background-color: #fcfcfc;
        }

        /* Kotak Catatan Keseluruhan */
        .notes-box { 
            font-size: 11px; 
            margin-bottom: 20px; 
            border: 1px solid #000; 
            padding: 10px 12px; 
            background: #fafafa;
        }
        .notes-box strong {
            display: block;
            margin-bottom: 3px;
        }

        /* Bagian Tanda Tangan & QR Code Digital */
        .footer-section { 
            width: 100%; 
            margin-top: 15px; 
            page-break-inside: avoid; 
        }
        .footer-table { 
            width: 100%; 
            border-collapse: collapse; 
        }
        .footer-table td { 
            vertical-align: top; 
            font-size: 11px; 
        }

        /* Styling QR Code sebagai Tanda Tangan Digital */
        .digital-sign-box {
            text-align: right;
            display: inline-block;
            float: right;
        }
        .qr-code {
            width: 70px;
            height: 70px;
            margin-bottom: 4px;
        }

        /* Tombol Cetak Manual */
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
    </style>
</head>
<body onload="window.print()">

    <!-- Tombol Cetak Manual (Hanya di Layar) -->
    <div class="no-print" style="text-align: center; margin-bottom: 15px;">
        <button onclick="window.print()" style="padding: 8px 18px; background: #000; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; font-size: 12px;">🖨️ Cetak Dokumen Resmi</button>
    </div>

    <div class="page-container">
        <!-- Header Dokumen -->
        <div class="header">
            <h2>LEMBAR AUDIT MEDIK DOOR TO BALLOON TIME</h2>
            <p>Instalasi Kardiovaskular / Cathlab — RSUD Blambangan</p>
        </div>

        <!-- Informasi Pasien -->
        <div class="info-card">
            <table class="info-table">
                <tr>
                    <td width="18%"><strong>Nama Pasien</strong></td>
                    <td width="32%">: <strong>{{ $action->patient->name ?? '-' }}</strong></td>
                    <td width="18%"><strong>Tgl Masuk IGD</strong></td>
                    <td width="32%">: {{ $action->d2b_igd_time ? \Carbon\Carbon::parse($action->d2b_igd_time)->format('d/m/Y H:i') : '-' }}</td>
                </tr>
                <tr>
                    <td><strong>No. Rekam Medis</strong></td>
                    <td>: {{ $action->patient->medical_record_number ?? '-' }}</td>
                    <td><strong>Diagnosa Utama</strong></td>
                    <td>: {{ $action->diagnosis_d2b ?? 'STEMI Infark Miokard Akut' }}</td>
                </tr>
            </table>
        </div>

        <!-- Tabel Kegiatan Audit D2B -->
        <table class="audit-table">
            <thead>
                <tr>
                    <th width="34%">Kegiatan Klinis</th>
                    <th width="18%">Waktu</th>
                    <th width="22%">Paraf & Nama Petugas</th>
                    <th width="26%">Keterangan / Hambatan & Masalah</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $rows = [
                        ['label' => 'Masuk IGD', 'key' => 'igd'],
                        ['label' => 'Triase', 'key' => 'triage'],
                        ['label' => 'Jam EKG pertama diperiksa', 'key' => 'ecg'],
                        ['label' => 'Pengkajian awal medik', 'key' => 'assessment'],
                        ['label' => 'Diagnosis ditegakkan', 'key' => 'diagnosis_est'],
                        ['label' => 'Konsulen PPCI diinformasikan', 'key' => 'ppci_consult'],
                        ['label' => 'Informasi disampaikan ke keluarga/pasien untuk PPCI', 'key' => 'family_info'],
                        ['label' => 'Keputusan persetujuan pasien/keluarga', 'key' => 'family_approval'],
                        ['label' => 'Pasien diantar ke cathlab', 'key' => 'to_cathlab'],
                        ['label' => 'Pasien sampai di cathlab', 'key' => 'arrival_cathlab'],
                        ['label' => 'Prosedur dimulai', 'key' => 'proc_start'],
                        ['label' => 'Tindakan lain selama PPCI', 'key' => 'other_action'],
                        ['label' => 'Balloon dilatasi', 'key' => 'balloon_dilatation'],
                        ['label' => 'Tindakan selesai', 'key' => 'proc_finish'],
                        ['label' => 'Transfer ke ruangan', 'key' => 'room_transfer'],
                    ];
                @endphp
                @foreach($rows as $row)
                <tr>
                    <td>{{ $row['label'] }}</td>
                    <td style="text-align: center;">{{ $action->{'d2b_' . $row['key'] . '_time'} ? \Carbon\Carbon::parse($action->{'d2b_' . $row['key'] . '_time'})->format('d/m H:i') : '' }}</td>
                    <td>{{ $action->{'d2b_' . $row['key'] . '_officer'} ?? '' }}</td>
                    <td>{{ $action->{'d2b_' . $row['key'] . '_notes'} ?? '' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Catatan Keseluruhan -->
        <div class="notes-box">
            <strong>Catatan Keseluruhan / Evaluasi Waktu:</strong>
            <span>{{ $action->d2b_general_notes ?? 'Tidak ada catatan hambatan khusus selama tindakan.' }}</span>
        </div>

        <!-- Tanda Tangan & QR Code Verifikasi Digital -->
        <div class="footer-section">
            <table class="footer-table">
                <tr>
                    <td width="50%">
                        <span style="font-size: 9.5px; color: #555;">
                            * Dokumen rekam medik ini dicetak secara elektronik dari sistem Cathlab Management.<br>
                            * Keabsahan dokumen dapat diverifikasi melalui QR Code di samping.
                        </span>
                    </td>
                    <td width="50%">
                        <div class="digital-sign-box">
                            Banyuwangi, {{ now()->format('d F Y') }}<br>
                            Verifikasi oleh Ka Instalasi/Divisi Cathlab<br>
                            <!-- QR Code diposisikan tepat di atas nama sebagai tanda tangan digital -->
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=120x120&data={{ urlencode(url()->current()) }}" class="qr-code" alt="QR TTD Digital"><br>
                            <strong><u>{{ $action->d2b_verified_name ?? 'Citra Indra Gustian, S.Kep, M.Kes.' }}</u></strong><br>
                            <span style="color: #333; font-size: 10px;">NIP. {{ $action->d2b_verified_nip ?? '19891231829381209312' }}</span>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</body>
</html>