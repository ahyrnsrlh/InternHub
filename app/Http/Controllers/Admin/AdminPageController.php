<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationManagementRequest;
use App\Models\Location;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminPageController extends Controller
{
    public function dashboard(): View
    {
        $summary = [
            'totalInterns' => 128,
            'attendanceToday' => 94,
            'activeInterns' => 117,
            'attendanceRate' => 73,
        ];

        $attendanceTrend = [
            ['heightClass' => 'h-20'],
            ['heightClass' => 'h-24'],
            ['heightClass' => 'h-16'],
            ['heightClass' => 'h-28'],
            ['heightClass' => 'h-32'],
            ['heightClass' => 'h-24'],
            ['heightClass' => 'h-36'],
        ];

        $recentCheckIns = [
            [
                'name' => 'Alex Rivers',
                'time' => '08:57 AM',
                'department' => 'Strategic Architecture',
                'gps_status' => 'valid',
            ],
            [
                'name' => 'Sarah Jenkins',
                'time' => '09:08 AM',
                'department' => 'Financial Analytics',
                'gps_status' => 'invalid',
            ],
            [
                'name' => 'Daniel Moore',
                'time' => '09:12 AM',
                'department' => 'Operations',
                'gps_status' => 'valid',
            ],
        ];

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

    public function attendance(): View
    {
        return view('pages.admin.attendance');
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

    public function reports(): View
    {
        return view('pages.admin.reports');
    }
}
