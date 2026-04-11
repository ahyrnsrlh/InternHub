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
            <td class="label">Catatan Disetujui</td>
            <td>{{ $summary['approved_logs'] }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Tanggal</th>
                <th>Departemen</th>
                <th>Aktivitas</th>
                <th>Deliverable</th>
                <th>Durasi</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($logs as $log)
                <tr>
                    <td>{{ optional($log->log_date)->format('d M Y') }}</td>
                    <td>{{ $log->department }}</td>
                    <td>{{ $log->summary }}</td>
                    <td>{{ $log->deliverable ?: '-' }}</td>
                    <td>{{ number_format((float) $log->hours, 2) }} jam</td>
                    <td>{{ ucfirst((string) $log->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="6">Data aktivitas tidak ditemukan.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
