<?php

namespace App\Http\Controllers\User;

use App\Models\DailyLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LogbookRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(Request $request): View
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $logs = DailyLog::query()
            ->where('user_id', Auth::id())
            ->when($startDate, fn ($query) => $query->whereDate('log_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('log_date', '<=', $endDate))
            ->latest('log_date')
            ->paginate(10)
            ->withQueryString();

        return view('pages.user.logbook', compact('logs'));
    }

    public function create(): View
    {
        return view('pages.user.logbook');
    }

    public function store(LogbookRequest $request): RedirectResponse
    {
        DailyLog::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'status' => $request->validated()['status'] ?? 'pending',
        ]);

        return redirect()->route('user.logbook.index')->with('status', 'Catatan harian berhasil disimpan.');
    }

    public function edit(string $logbook): View
    {
        $log = DailyLog::query()
            ->where('id', $logbook)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.user.logbook', compact('log'));
    }

    public function update(LogbookRequest $request, string $logbook): RedirectResponse
    {
        $log = DailyLog::query()
            ->where('id', $logbook)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $log->update($request->validated());

        return redirect()->route('user.logbook.index')->with('status', 'Catatan harian berhasil diperbarui.');
    }

    public function destroy(string $logbook): RedirectResponse
    {
        $log = DailyLog::query()
            ->where('id', $logbook)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $log->delete();

        return redirect()->route('user.logbook.index')->with('status', 'Catatan harian berhasil dihapus.');
    }

    public function exportPdf(Request $request)
    {
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');

        $logs = DailyLog::query()
            ->where('user_id', Auth::id())
            ->when($startDate, fn ($query) => $query->whereDate('log_date', '>=', $startDate))
            ->when($endDate, fn ($query) => $query->whereDate('log_date', '<=', $endDate))
            ->latest('log_date')
            ->get();

        $summary = [
            'total_logs' => $logs->count(),
            'total_hours' => (float) $logs->sum('hours'),
            'approved_logs' => $logs->where('status', 'approved')->count(),
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
