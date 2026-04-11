<?php

namespace App\Http\Controllers\User;

use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserDashboardRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class UserController extends Controller
{
    public function index(): View
    {
        $userId = (int) Auth::id();

        $totalAttendance = Attendance::query()
            ->where('user_id', $userId)
            ->count();

        $validAttendance = Attendance::query()
            ->where('user_id', $userId)
            ->where('status', 'valid')
            ->count();

        $totalActivities = DailyLog::query()
            ->where('user_id', $userId)
            ->count();

        $activitiesThisWeek = DailyLog::query()
            ->where('user_id', $userId)
            ->whereDate('log_date', '>=', now()->subDays(7)->toDateString())
            ->count();

        $recentAttendances = Attendance::query()
            ->where('user_id', $userId)
            ->latest('check_in_time')
            ->limit(5)
            ->get();

        $checkedInToday = Attendance::query()
            ->where('user_id', $userId)
            ->whereDate('check_in_time', now()->toDateString())
            ->exists();

        $attendanceRate = $totalAttendance > 0
            ? round(($validAttendance / $totalAttendance) * 100, 1)
            : 0;

        return view('pages.user.dashboard', [
            'user' => Auth::user(),
            'summary' => [
                'total_attendance' => $totalAttendance,
                'valid_attendance' => $validAttendance,
                'attendance_rate' => $attendanceRate,
                'total_activities' => $totalActivities,
                'activities_this_week' => $activitiesThisWeek,
                'checked_in_today' => $checkedInToday,
            ],
            'recentAttendances' => $recentAttendances,
        ]);
    }

    public function create(): View
    {
        return view('pages.user.dashboard', [
            'user' => Auth::user(),
        ]);
    }

    public function store(UserDashboardRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('user.dashboard.index')->with('status', 'Preferensi beranda berhasil disimpan.');
    }

    public function edit(string $dashboard): View
    {
        $user = User::query()->findOrFail($dashboard);
        abort_unless($user->id === (int) Auth::id(), 403);

        return view('pages.user.dashboard', compact('user'));
    }

    public function update(UserDashboardRequest $request, string $dashboard): RedirectResponse
    {
        $user = User::query()->findOrFail($dashboard);
        abort_unless($user->id === (int) $request->user()->id, 403);

        $user->update($request->validated());

        return redirect()->route('user.dashboard.index')->with('status', 'Data pengguna pada beranda berhasil diperbarui.');
    }

    public function destroy(string $dashboard): RedirectResponse
    {
        $user = User::query()->findOrFail($dashboard);
        abort_unless($user->id === (int) Auth::id(), 403);

        $user->update(['status' => 'inactive']);

        return redirect()->route('user.dashboard.index')->with('status', 'Status akun berhasil diubah menjadi tidak aktif.');
    }
}
