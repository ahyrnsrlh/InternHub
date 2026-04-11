<?php

namespace App\Http\Controllers\User;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
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

        return redirect()->route('user.profile.index')->with('status', 'Profil berhasil disimpan.');
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

        return redirect()->route('user.profile.index')->with('status', 'Profil berhasil diperbarui.');
    }

    public function destroy(string $profile): RedirectResponse
    {
        $user = User::query()->findOrFail($profile);
        abort_unless($user->id === (int) Auth::id(), 403);

        $user->delete();

        return redirect('/')->with('status', 'Profil berhasil dihapus.');
    }

    public function storeFace(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'face_image' => ['required', 'string'],
            'face_descriptor' => ['required', 'string'],
        ]);

        if (! preg_match('/^data:image\/(png|jpeg|jpg);base64,/', $validated['face_image'])) {
            throw ValidationException::withMessages([
                'face_image' => 'Format gambar wajah tidak valid.',
            ]);
        }

        $descriptor = json_decode($validated['face_descriptor'], true);
        if (! is_array($descriptor) || count($descriptor) !== 128) {
            throw ValidationException::withMessages([
                'face_descriptor' => 'Deskriptor wajah tidak valid. Silakan ulangi perekaman.',
            ]);
        }

        foreach ($descriptor as $value) {
            if (! is_numeric($value)) {
                throw ValidationException::withMessages([
                    'face_descriptor' => 'Deskriptor wajah berisi nilai yang tidak valid.',
                ]);
            }
        }

        $base64 = preg_replace('/^data:image\/(png|jpeg|jpg);base64,/', '', $validated['face_image']);
        $binary = base64_decode((string) $base64, true);

        if ($binary === false) {
            throw ValidationException::withMessages([
                'face_image' => 'Gagal memproses gambar wajah. Silakan coba lagi.',
            ]);
        }

        $user = $request->user();
        $filePath = 'profile-photos/user-'.$user->id.'-'.now()->format('YmdHis').'.jpg';
        Storage::disk('public')->put($filePath, $binary);

        if ($user->profile_photo) {
            Storage::disk('public')->delete($user->profile_photo);
        }

        $user->update([
            'profile_photo' => $filePath,
            'face_descriptor' => array_map(static fn ($item) => (float) $item, $descriptor),
            'face_registered' => true,
        ]);

        return redirect()->route('user.profile.index')->with('status', 'Wajah berhasil direkam.');
    }
}
