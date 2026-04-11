<?php

namespace App\Http\Controllers\User;

use App\Models\Attendance;
use App\Models\DailyLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ReportRequest;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $filterDate = request()->query('date');
        $reportQuery = $this->attendanceReportQuery($filterDate);

        $reports = $reportQuery->paginate(10)->withQueryString();

        $summaryQuery = $this->attendanceReportQuery($filterDate);
        $summary = [
            'total_attendance' => (clone $summaryQuery)->count(),
            'valid_attendance' => (clone $summaryQuery)->where('status', 'valid')->count(),
        ];

        return view('pages.user.reports', compact('reports', 'filterDate', 'summary'));
    }

    public function exportPdf(Request $request)
    {
        $filterDate = $request->query('date');
        $reports = $this->attendanceReportQuery($filterDate)->get();

        $summaryQuery = $this->attendanceReportQuery($filterDate);
        $summary = [
            'total_attendance' => (clone $summaryQuery)->count(),
            'valid_attendance' => (clone $summaryQuery)->where('status', 'valid')->count(),
        ];

        $pdf = Pdf::loadView('pages.user.reports-pdf', [
            'reports' => $reports,
            'summary' => $summary,
            'filterDate' => $filterDate,
            'user' => $request->user(),
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');

        return $pdf->download('attendance-report-'.now()->format('YmdHis').'.pdf');
    }

    public function create(): View
    {
        return view('pages.user.reports');
    }

    public function store(ReportRequest $request): RedirectResponse
    {
        DailyLog::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
            'department' => $request->validated()['department'] ?? ($request->user()->department ?? 'General'),
            'status' => $request->validated()['status'] ?? 'pending',
        ]);

        return redirect()->route('user.reports.index')->with('status', 'Report saved successfully.');
    }

    public function edit(string $report): View
    {
        $reportData = DailyLog::query()
            ->where('id', $report)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.user.reports', compact('reportData'));
    }

    public function update(ReportRequest $request, string $report): RedirectResponse
    {
        $reportData = DailyLog::query()
            ->where('id', $report)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $reportData->update($request->validated());

        return redirect()->route('user.reports.index')->with('status', 'Report updated successfully.');
    }

    public function destroy(string $report): RedirectResponse
    {
        $reportData = DailyLog::query()
            ->where('id', $report)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $reportData->delete();

        return redirect()->route('user.reports.index')->with('status', 'Report deleted successfully.');
    }

    public function recap(): View
    {
        $recap = [
            'total_logs' => DailyLog::query()->where('user_id', Auth::id())->count(),
            'approved_logs' => DailyLog::query()->where('user_id', Auth::id())->where('status', 'approved')->count(),
            'pending_logs' => DailyLog::query()->where('user_id', Auth::id())->where('status', 'pending')->count(),
        ];

        return view('pages.user.recap', compact('recap'));
    }

    private function attendanceReportQuery(?string $filterDate)
    {
        return Attendance::query()
            ->with('location')
            ->where('user_id', Auth::id())
            ->when($filterDate, fn ($query) => $query->whereDate('check_in_time', $filterDate))
            ->latest('check_in_time');
    }
}
