<?php

namespace App\Http\Controllers\User;

use App\Models\Attendance;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $logs = Attendance::query()
            ->with('location:id,name,address')
            ->where('user_id', Auth::id())
            ->whereNotNull('check_in_time')
            ->when($startDate, fn ($query) => $query->whereDate('check_in_time', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('check_in_time', '<=', $endDate))
            ->latest('check_in_time')
            ->paginate(10)
            ->withQueryString();

        return view('pages.user.logbook', compact('logs'));
    }

    public function create(): RedirectResponse
    {
        return redirect()->route('user.logbook.index');
    }

    public function store(Request $request): RedirectResponse
    {
        return redirect()->route('user.logbook.index')->withErrors([
            'logbook' => 'Laporan Harian diambil otomatis dari data presensi. Input manual tidak diperlukan.',
        ]);
    }

    public function edit(string $logbook): RedirectResponse
    {
        return redirect()->route('user.logbook.index');
    }

    public function update(Request $request, string $logbook): RedirectResponse
    {
        return redirect()->route('user.logbook.index')->withErrors([
            'logbook' => 'Laporan Harian diambil otomatis dari data presensi dan tidak dapat diedit manual.',
        ]);
    }

    public function destroy(string $logbook): RedirectResponse
    {
        return redirect()->route('user.logbook.index')->withErrors([
            'logbook' => 'Laporan Harian mengikuti data presensi dan tidak dapat dihapus manual.',
        ]);
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $logs = Attendance::query()
            ->with('location:id,name,address')
            ->where('user_id', Auth::id())
            ->whereNotNull('check_in_time')
            ->when($startDate, fn ($query) => $query->whereDate('check_in_time', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('check_in_time', '<=', $endDate))
            ->latest('check_in_time')
            ->get();

        $totalHours = $logs->sum(function (Attendance $attendance): float {
            if (! $attendance->check_in_time || ! $attendance->check_out_time) {
                return 0;
            }

            return max(0, Carbon::parse($attendance->check_in_time)->floatDiffInHours(Carbon::parse($attendance->check_out_time)));
        });

        $summary = [
            'total_logs' => $logs->count(),
            'total_hours' => (float) $totalHours,
            'valid_logs' => $logs->where('status', 'valid')->count(),
            'invalid_logs' => $logs->where('status', 'invalid')->count(),
        ];

        $pdf = Pdf::loadView('pages.user.logbook-pdf', [
            'logs' => $logs,
            'summary' => $summary,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'user' => $request->user(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('laporan-logbook-'.now()->format('YmdHis').'.pdf');
    }
}
