<?php

namespace App\Http\Controllers\User;

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
        return view('pages.user.dashboard', [
            'user' => Auth::user(),
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

        return redirect()->route('user.dashboard.index')->with('status', 'Dashboard preferences saved.');
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

        return redirect()->route('user.dashboard.index')->with('status', 'Dashboard user data updated.');
    }

    public function destroy(string $dashboard): RedirectResponse
    {
        $user = User::query()->findOrFail($dashboard);
        abort_unless($user->id === (int) Auth::id(), 403);

        $user->update(['status' => 'inactive']);

        return redirect()->route('user.dashboard.index')->with('status', 'Account status set to inactive.');
    }
}
