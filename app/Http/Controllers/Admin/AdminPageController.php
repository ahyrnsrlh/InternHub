<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LocationManagementRequest;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
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

    public function interns(): View
    {
        return view('pages.admin.interns');
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

        return redirect()->route('internhub.admin.locations')->with('status', 'Location created successfully.');
    }

    public function updateLocation(LocationManagementRequest $request, Location $location): RedirectResponse
    {
        $location->update($request->validated());

        return redirect()->route('internhub.admin.locations')->with('status', 'Location updated successfully.');
    }

    public function destroyLocation(Location $location): RedirectResponse
    {
        $location->delete();

        return redirect()->route('internhub.admin.locations')->with('status', 'Location deleted successfully.');
    }

    public function reports(): View
    {
        return view('pages.admin.reports');
    }
}
