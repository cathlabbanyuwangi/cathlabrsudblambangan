<table border="1">
    <thead>
        <tr>
            <th>No RM</th>
            <th>Nama Pasien</th>
            <th>No Telp</th>
            <th>Sumber</th>
            <th>Jaminan</th>
            <th>Status</th>
            <th>Kesediaan</th>
            <th>Alasan Penolakan</th>
            <th>Pemanggil</th>
            <th>Waktu Panggil</th>
            <th>Jadwal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($patients as $p)
        <tr>
            <td>{{ $p->medical_record_number }}</td>
            <td>{{ $p->name }}</td>
            <td>{{ $p->patient_phone }}</td>
            <td>{{ $p->source }}</td>
            <td>{{ $p->insurance->name ?? '-' }}</td>
            <td>{{ $p->status }}</td>
            <td>{{ $p->willingness }}</td>
            <td>{{ $p->unwillingness_reason ?? '-' }}</td>
            <td>{{ $p->caller->name ?? '-' }}</td>
            <td>{{ $p->called_at }}</td>
            <td>{{ $p->scheduled_at }}</td>
        </tr>
        @endforeach
    </tbody>
</table>