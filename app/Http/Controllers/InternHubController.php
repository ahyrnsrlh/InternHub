<?php

namespace App\Http\Controllers;

use App\Models\AttendanceRecord;
use App\Models\DailyLog;
use App\Models\MentorReview;
use App\Models\Program;
use App\Models\User;
use Illuminate\Support\Carbon;

class InternHubController extends Controller
{
    public function dashboard()
    {
        $intern = User::query()
            ->where('role', 'intern')
            ->with(['attendanceRecords' => fn ($q) => $q->latest('work_date')->limit(30), 'dailyLogs' => fn ($q) => $q->latest('log_date')->limit(12)])
            ->first();

        $attendanceStreak = $intern
            ? $intern->attendanceRecords->where('status', 'present')->take(10)->count()
            : 0;

        $currentMonthLogs = $intern
            ? $intern->dailyLogs->filter(fn ($log) => $log->log_date?->isCurrentMonth())->count()
            : 0;

        $progress = min(100, $currentMonthLogs * 20);

        $metrics = [
            ['label' => 'Overall Status', 'value' => $intern?->status === 'active' ? 'Operational Excellence' : 'Needs Attention', 'hint' => 'Current Grade A+'],
            ['label' => 'Attendance Streak', 'value' => $attendanceStreak . ' Days', 'hint' => 'Derived from last 10 sessions'],
            ['label' => 'Logbook Progress', 'value' => $progress . '%', 'hint' => $currentMonthLogs . ' entries logged this month'],
        ];

        $activities = DailyLog::query()
            ->with('user')
            ->latest('log_date')
            ->limit(3)
            ->get()
            ->map(fn ($log) => [
                'title' => $log->status === 'approved' ? 'Logbook Approved' : 'Logbook Update',
                'time' => optional($log->log_date)->diffForHumans() ?? 'recently',
                'detail' => ($log->user?->name ?? 'Intern') . ' - ' . str($log->summary)->limit(70),
            ]);

        return view('pages.dashboard', compact('metrics', 'activities'));
    }

    public function attendance()
    {
        $intern = User::query()->where('role', 'intern')->first();

        $records = AttendanceRecord::query()
            ->when($intern, fn ($q) => $q->where('user_id', $intern->id))
            ->latest('work_date')
            ->limit(10)
            ->get();

        $summary = [
            'present_days' => $records->where('status', 'present')->count(),
            'late_days' => $records->where('status', 'late')->count(),
            'remaining_leaves' => max(0, 12 - $records->where('status', 'on_leave')->count()),
            'current_status' => $records->first()?->status ?? 'out',
        ];

        return view('pages.attendance-tracking', compact('records', 'summary'));
    }

    public function dailyLogbook()
    {
        $intern = User::query()->where('role', 'intern')->first();

        $history = DailyLog::query()
            ->when($intern, fn ($q) => $q->where('user_id', $intern->id))
            ->latest('log_date')
            ->limit(8)
            ->get();

        return view('pages.daily-logbook', compact('history'));
    }

    public function mentorReviewPanel()
    {
        $mentor = User::query()->where('role', 'mentor')->with('mentees')->first();

        $assignedInterns = $mentor?->mentees ?? collect();

        $reviewQueue = MentorReview::query()
            ->with(['dailyLog.user', 'mentor'])
            ->when($mentor, fn ($q) => $q->where('mentor_id', $mentor->id))
            ->latest('created_at')
            ->limit(5)
            ->get();

        return view('pages.mentor-review-panel', compact('assignedInterns', 'reviewQueue'));
    }

    public function adminControlCenter()
    {
        $interns = User::query()->where('role', 'intern')->get();
        $totalInterns = max(1, $interns->count());
        $placedInterns = $interns->whereNotNull('placement')->count();
        $compliantInterns = DailyLog::query()
            ->whereDate('log_date', '>=', Carbon::now()->subDays(7))
            ->distinct('user_id')
            ->count('user_id');

        $stats = [
            ['label' => 'Active Interns', 'value' => (string) $interns->where('status', 'active')->count(), 'delta' => '+12%'],
            ['label' => 'Placement Rate', 'value' => number_format(($placedInterns / $totalInterns) * 100, 1) . '%', 'delta' => '+3.1%'],
            ['label' => 'Logbook Compliance', 'value' => number_format(($compliantInterns / $totalInterns) * 100, 0) . '%', 'delta' => '-2.4%'],
        ];

        $users = User::query()->whereIn('role', ['intern', 'mentor', 'admin'])->latest()->limit(12)->get();
        $programs = Program::query()->where('is_active', true)->latest()->limit(6)->get();

        return view('pages.admin-control-center', compact('stats', 'users', 'programs'));
    }
}
