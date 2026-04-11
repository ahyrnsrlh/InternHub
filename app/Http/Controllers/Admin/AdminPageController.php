<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationManagementRequest;
use App\Models\Attendance;
use App\Models\DailyLog;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function dashboard(): View
    {
        $today = now()->toDateString();

        $totalInterns = User::query()
            ->whereIn('role', [User::ROLE_INTERN, User::ROLE_USER])
            ->count();

        $activeInterns = User::query()
            ->whereIn('role', [User::ROLE_INTERN, User::ROLE_USER])
            ->where('status', 'active')
            ->count();

        $attendanceToday = Attendance::query()
            ->whereDate('check_in_time', $today)
            ->count();

        $validAttendanceToday = Attendance::query()
            ->whereDate('check_in_time', $today)
            ->where('status', 'valid')
            ->count();

        $invalidAttendanceToday = max(0, $attendanceToday - $validAttendanceToday);
        $attendanceRate = $attendanceToday > 0
            ? round(($validAttendanceToday / $attendanceToday) * 100, 1)
            : 0;

        $recentActivitiesCount = DailyLog::query()
            ->whereDate('log_date', '>=', now()->subDays(7)->toDateString())
            ->count();

        $summary = [
            'totalInterns' => $totalInterns,
            'attendanceToday' => $attendanceToday,
            'activeInterns' => $activeInterns,
            'attendanceRate' => $attendanceRate,
            'validAttendanceToday' => $validAttendanceToday,
            'invalidAttendanceToday' => $invalidAttendanceToday,
            'recentActivitiesCount' => $recentActivitiesCount,
        ];

        $attendanceTrend = collect(range(6, 0))
            ->map(function (int $dayOffset) {
                $day = Carbon::now()->subDays($dayOffset);
                $count = Attendance::query()->whereDate('check_in_time', $day->toDateString())->count();

                return [
                    'date_label' => $day->format('d M'),
                    'count' => $count,
                ];
            })
            ->values()
            ->all();

        $recentCheckIns = Attendance::query()
            ->with('user')
            ->latest('check_in_time')
            ->limit(6)
            ->get()
            ->map(function (Attendance $attendance) {
                return [
                    'name' => $attendance->user?->name ?? 'Peserta',
                    'time' => optional($attendance->check_in_time)->format('d M Y H:i') ?? '-',
                    'department' => $attendance->user?->department ?? '-',
                    'gps_status' => $attendance->status,
                ];
            })
            ->values()
            ->all();

        return view('pages.admin.dashboard', compact('summary', 'attendanceTrend', 'recentCheckIns'));
    }

    public function interns(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $status = (string) $request->query('status', '');

        $interns = User::query()
            ->whereIn('role', [User::ROLE_INTERN, User::ROLE_USER])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('placement', 'like', "%{$search}%");
                });
            })
            ->when(in_array($status, ['active', 'inactive'], true), fn ($query) => $query->where('status', $status))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('pages.admin.interns', compact('interns', 'search', 'status'));
    }

    public function storeIntern(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', 'unique:users,email'],
            'placement' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['required', 'string', 'min:8', 'max:64'],
        ]);

        User::query()->create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'placement' => $validated['placement'],
            'status' => $validated['status'],
            'role' => User::ROLE_INTERN,
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('internhub.admin.interns')->with('status', 'Peserta magang berhasil ditambahkan.');
    }

    public function updateIntern(Request $request, User $internUser): RedirectResponse
    {
        if (!in_array($internUser->role, [User::ROLE_INTERN, User::ROLE_USER], true)) {
            return redirect()->route('internhub.admin.interns')->with('error', 'Data yang dipilih bukan peserta magang.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email:rfc,dns', 'max:255', Rule::unique('users', 'email')->ignore($internUser->id)],
            'placement' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'password' => ['nullable', 'string', 'min:8', 'max:64'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'placement' => $validated['placement'],
            'status' => $validated['status'],
            'role' => User::ROLE_INTERN,
        ];

        if (!empty($validated['password'])) {
            $payload['password'] = Hash::make($validated['password']);
        }

        $internUser->update($payload);

        return redirect()->route('internhub.admin.interns')->with('status', 'Data peserta magang berhasil diperbarui.');
    }

    public function destroyIntern(User $internUser): RedirectResponse
    {
        if (!in_array($internUser->role, [User::ROLE_INTERN, User::ROLE_USER], true)) {
            return redirect()->route('internhub.admin.interns')->with('error', 'Data yang dipilih bukan peserta magang.');
        }

        $internUser->delete();

        return redirect()->route('internhub.admin.interns')->with('status', 'Data peserta magang berhasil dihapus.');
    }

    public function internDetail(string $intern): View
    {
        return view('pages.admin.intern-detail', compact('intern'));
    }

    public function attendance(Request $request): View
    {
        $filterDate = (string) $request->query('date', '');
        $filterUserId = (string) $request->query('user_id', '');
        $filterStatus = (string) $request->query('status', '');

        $attendanceQuery = Attendance::query()
            ->with(['user:id,name', 'location:id,name'])
            ->when($filterDate !== '', fn ($query) => $query->whereDate('check_in_time', $filterDate))
            ->when($filterUserId !== '', fn ($query) => $query->where('user_id', (int) $filterUserId))
            ->when(in_array($filterStatus, ['valid', 'invalid'], true), fn ($query) => $query->where('status', $filterStatus))
            ->latest('check_in_time');

        $attendances = $attendanceQuery->paginate(12)->withQueryString();
        $internOptions = User::query()
            ->whereIn('role', [User::ROLE_INTERN, User::ROLE_USER])
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('pages.admin.attendance', compact('attendances', 'internOptions', 'filterDate', 'filterUserId', 'filterStatus'));
    }

    public function locations(): View
    {
        $locations = Location::query()
            ->orderBy('name')
            ->get(['id', 'name', 'address', 'latitude', 'longitude', 'radius_meters', 'status']);

        return view('pages.admin.locations', compact('locations'));
    }

    public function storeLocation(LocationManagementRequest $request): RedirectResponse
    {
        Location::query()->create($request->validated());

        return redirect()->route('internhub.admin.locations')->with('status', 'Lokasi magang berhasil ditambahkan.');
    }

    public function updateLocation(LocationManagementRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('internhub.admin.locations')->with('status', 'Lokasi magang berhasil diperbarui.');
    }

    public function destroyLocation(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('internhub.admin.locations')->with('status', 'Lokasi magang berhasil dihapus.');
    }

    public function reports(Request $request): View
    {
        $startDate = (string) $request->query('start_date', now()->subDays(6)->toDateString());
        $endDate = (string) $request->query('end_date', now()->toDateString());

        $dailyReports = Attendance::query()
            ->selectRaw('DATE(check_in_time) as report_date')
            ->selectRaw('COUNT(*) as total_attendance')
            ->selectRaw("SUM(CASE WHEN status = 'valid' THEN 1 ELSE 0 END) as valid_attendance")
            ->selectRaw("SUM(CASE WHEN status = 'invalid' THEN 1 ELSE 0 END) as invalid_attendance")
            ->whereDate('check_in_time', '>=', $startDate)
            ->whereDate('check_in_time', '<=', $endDate)
            ->groupBy('report_date')
            ->orderByDesc('report_date')
            ->get();

        $summary = [
            'total_attendance' => (int) $dailyReports->sum('total_attendance'),
            'valid_attendance' => (int) $dailyReports->sum('valid_attendance'),
            'invalid_attendance' => (int) $dailyReports->sum('invalid_attendance'),
        ];

        $attendanceRate = $summary['total_attendance'] > 0
            ? round(($summary['valid_attendance'] / $summary['total_attendance']) * 100, 1)
            : 0;

        return view('pages.admin.reports', compact('dailyReports', 'summary', 'attendanceRate', 'startDate', 'endDate'));
    }
}
