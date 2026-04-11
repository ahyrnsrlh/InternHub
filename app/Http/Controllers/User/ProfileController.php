<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(): View
    {
        return view('pages.user.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function create(): View
    {
        return view('pages.user.profile');
    }

    public function store(UserProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->validated());

        return redirect()->route('user.profile.index')->with('status', 'Profile saved successfully.');
    }

    public function edit(string $profile): View
    {
        $user = User::query()->findOrFail($profile);
        abort_unless($user->id === (int) Auth::id(), 403);

        return view('pages.user.profile', compact('user'));
    }

    public function update(UserProfileRequest $request, string $profile): RedirectResponse
    {
        $user = User::query()->findOrFail($profile);
        abort_unless($user->id === (int) $request->user()->id, 403);

        $user->update($request->validated());

        return redirect()->route('user.profile.index')->with('status', 'Profile updated successfully.');
    }

    public function destroy(string $profile): RedirectResponse
    {
        $user = User::query()->findOrFail($profile);
        abort_unless($user->id === (int) Auth::id(), 403);

        $user->delete();

        return redirect('/')->with('status', 'Profile deleted successfully.');
    }
}
