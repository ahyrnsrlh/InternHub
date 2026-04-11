<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Aktivitas Harian</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            margin: 24px;
        }
        .title {
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .meta {
            color: #4b5563;
            margin-bottom: 2px;
        }
        .summary {
            margin: 16px 0;
            width: 100%;
            border-collapse: collapse;
        }
        .summary td {
            border: 1px solid #e5e7eb;
            padding: 8px 10px;
        }
        .summary .label {
            color: #6b7280;
            width: 50%;
        }
        .table {
            width: 100%;
            border-collapse: collapse;
        }
        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        .table th {
            background: #f3f4f6;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="title">Laporan Aktivitas Harian</div>
    <div class="meta">Peserta: {{ $user->name }}</div>
    <div class="meta">Email: {{ $user->email }}</div>
    <div class="meta">
        Rentang: {{ $startDate ? \Illuminate\Support\Carbon::parse($startDate)->format('d M Y') : 'Semua Tanggal' }}
        -
        {{ $endDate ? \Illuminate\Support\Carbon::parse($endDate)->format('d M Y') : 'Semua Tanggal' }}
    </div>
    <div class="meta">Dicetak Pada: {{ $generatedAt->format('d M Y H:i') }}</div>

    <table class="summary">
        <tr>
            <td class="label">Total Catatan</td>
            <td>{{ $summary['total_logs'] }}</td>
        </tr>
        <tr>
            <td class="label">Total Jam Aktivitas</td>
            <td>{{ number_format($summary['total_hours'], 2) }} jam</td>
        </tr>
        <tr>
            <td class="label">Presensi Valid</td>
            <td>{{ $summary['valid_logs'] }}</td>
        </tr>
        <tr>
            <td class="label">Presensi Tidak Valid</td>
            <td>{{ $summary['invalid_logs'] }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Jam Masuk</th>
                <th>Jam Pulang</th>
                <th>Rencana Kegiatan</th>
                <th>Realisasi Kegiatan</th>
                <th>Lokasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ optional($log->check_in_time)->format('d M Y') }}</td>
                    <td>{{ optional($log->check_in_time)->format('H:i') ?: '-' }}</td>
                    <td>{{ optional($log->check_out_time)->format('H:i') ?: '-' }}</td>
                    <td>{{ $log->check_in_note ?: '-' }}</td>
                    <td>{{ $log->check_out_note ?: '-' }}</td>
                    <td>{{ $log->location?->name ?: '-' }}</td>
                    <td>{{ ucfirst((string) $log->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7">Data Laporan Harian dari presensi tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
