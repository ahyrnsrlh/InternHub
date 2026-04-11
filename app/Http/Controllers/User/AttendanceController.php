<?php

namespace App\Http\Controllers\User;

use App\Models\AttendanceRecord;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\AttendanceRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AttendanceController extends Controller
{
    public function index(): View
    {
        $attendances = AttendanceRecord::query()
            ->where('user_id', Auth::id())
            ->latest('work_date')
            ->paginate(10);

        return view('pages.user.attendance', compact('attendances'));
    }

    public function create(): View
    {
        return view('pages.user.attendance');
    }

    public function store(AttendanceRequest $request): RedirectResponse
    {
        AttendanceRecord::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('user.attendance.index')->with('status', 'Attendance saved successfully.');
    }

    public function edit(string $attendance): View
    {
        $attendanceRecord = AttendanceRecord::query()
            ->where('id', $attendance)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.user.attendance', compact('attendanceRecord'));
    }

    public function update(AttendanceRequest $request, string $attendance): RedirectResponse
    {
        $attendanceRecord = AttendanceRecord::query()
            ->where('id', $attendance)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $attendanceRecord->update($request->validated());

        return redirect()->route('user.attendance.index')->with('status', 'Attendance updated successfully.');
    }

    public function destroy(string $attendance): RedirectResponse
    {
        $attendanceRecord = AttendanceRecord::query()
            ->where('id', $attendance)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $attendanceRecord->delete();

        return redirect()->route('user.attendance.index')->with('status', 'Attendance deleted successfully.');
    }
}
