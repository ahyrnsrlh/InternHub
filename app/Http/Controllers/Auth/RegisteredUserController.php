<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'face_descriptor' => ['required', 'string'],
            'liveness_verified' => ['required', 'accepted'],
        ]);

        $descriptor = json_decode((string) $request->input('face_descriptor'), true);

        if (! is_array($descriptor) || count($descriptor) !== 128) {
            throw ValidationException::withMessages([
                'face_descriptor' => 'Invalid face descriptor data. Please recapture your face.',
            ]);
        }

        foreach ($descriptor as $value) {
            if (! is_numeric($value)) {
                throw ValidationException::withMessages([
                    'face_descriptor' => 'Face descriptor contains invalid values.',
                ]);
            }
        }

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'face_descriptor' => array_map(static fn ($item) => (float) $item, $descriptor),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
