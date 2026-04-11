<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureFaceRegistered
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (
            $user
            && $user->hasRole(User::ROLE_INTERN, User::ROLE_USER)
            && (! $user->face_registered || empty($user->face_descriptor))
        ) {
            return redirect()->route('user.profile.index')
                ->with('status', 'Silakan lengkapi profil dengan merekam wajah terlebih dahulu.');
        }

        return $next($request);
    }
}
