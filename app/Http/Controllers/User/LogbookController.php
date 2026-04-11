<?php

namespace App\Http\Controllers\User;

use App\Models\DailyLog;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LogbookRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LogbookController extends Controller
{
    public function index(): View
    {
        $logs = DailyLog::query()
            ->where('user_id', Auth::id())
            ->latest('log_date')
            ->paginate(10);

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

        return redirect()->route('user.logbook.index')->with('status', 'Logbook entry saved successfully.');
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

        return redirect()->route('user.logbook.index')->with('status', 'Logbook entry updated successfully.');
    }

    public function destroy(string $logbook): RedirectResponse
    {
        $log = DailyLog::query()
            ->where('id', $logbook)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $log->delete();

        return redirect()->route('user.logbook.index')->with('status', 'Logbook entry deleted successfully.');
    }
}
