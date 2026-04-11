<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Report</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 12px;
            margin: 24px;
        }

        .header {
            margin-bottom: 18px;
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
            margin: 18px 0;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            width: 100%;
            border-collapse: collapse;
        }

        .summary td {
            padding: 10px;
            border: 1px solid #e5e7eb;
        }

        .summary .label {
            color: #6b7280;
            width: 50%;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            border: 1px solid #e5e7eb;
            padding: 8px;
            text-align: left;
        }

        .table th {
            background: #f3f4f6;
            font-weight: 700;
        }

        .status-valid {
            color: #166534;
            font-weight: 700;
        }

        .status-invalid {
            color: #991b1b;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="title">Attendance Report</div>
        <div class="meta">Intern: {{ $user->name }}</div>
        <div class="meta">Email: {{ $user->email }}</div>
        <div class="meta">Date Filter: {{ $filterDate ? \Illuminate\Support\Carbon::parse($filterDate)->format('d M Y') : 'All Dates' }}</div>
        <div class="meta">Generated At: {{ $generatedAt->format('d M Y H:i') }}</div>
    </div>

    <table class="summary">
        <tr>
            <td class="label">Total Attendance</td>
            <td>{{ $summary['total_attendance'] }}</td>
        </tr>
        <tr>
            <td class="label">Valid Attendance</td>
            <td>{{ $summary['valid_attendance'] }}</td>
        </tr>
    </table>

    <table class="table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Location</th>
                <th>Check-In</th>
                <th>Check-Out</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
                <tr>
                    <td>{{ optional($report->check_in_time)->format('d M Y') ?? '-' }}</td>
                    <td>{{ $report->location->name ?? '-' }}</td>
                    <td>{{ optional($report->check_in_time)->format('H:i') ?? '-' }}</td>
                    <td>{{ optional($report->check_out_time)->format('H:i') ?? '-' }}</td>
                    <td class="{{ $report->status === 'valid' ? 'status-valid' : 'status-invalid' }}">{{ ucfirst($report->status) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No attendance data found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
