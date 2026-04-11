<?php

namespace App\Http\Controllers\User;

use App\Models\DailyLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\ReportRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(): View
    {
        $reports = DailyLog::query()
            ->where('user_id', Auth::id())
            ->latest('log_date')
            ->paginate(10);

        return view('pages.user.reports', compact('reports'));
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
}
