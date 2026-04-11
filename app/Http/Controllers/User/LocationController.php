<?php

namespace App\Http\Controllers\User;

use App\Models\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LocationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $userLocation = Location::query()
            ->where('user_id', Auth::id())
            ->latest('id')
            ->first();

        $hasLocation = (bool) $userLocation;

        return view('pages.user.locations', compact('userLocation', 'hasLocation'));
    }

    public function create(): View
    {
        return view('pages.user.locations');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        $alreadyExists = Location::query()
            ->where('user_id', $request->user()->id)
            ->exists();

        if ($alreadyExists) {
            return redirect()->route('user.locations.index')->withErrors([
                'location' => 'Anda sudah memiliki lokasi magang. Silakan ubah lokasi yang ada.',
            ]);
        }

        Location::query()->create([
            ...$request->validated(),
            'user_id' => $request->user()->id,
        ]);

        return redirect()->route('user.locations.index')->with('status', 'Lokasi berhasil disimpan.');
    }

    public function edit(string $location): View
    {
        $locationData = Location::query()
            ->where('id', $location)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        return view('pages.user.locations', compact('locationData'));
    }

    public function update(LocationRequest $request, string $location): RedirectResponse
    {
        $locationData = Location::query()
            ->where('id', $location)
            ->where('user_id', $request->user()->id)
            ->firstOrFail();

        $locationData->update($request->validated());

        return redirect()->route('user.locations.index')->with('status', 'Lokasi berhasil diperbarui.');
    }

    public function destroy(string $location): RedirectResponse
    {
        $locationData = Location::query()
            ->where('id', $location)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $locationData->delete();

        return redirect()->route('user.locations.index')->with('status', 'Lokasi berhasil dihapus.');
    }

    public function map(): View
    {
        $locations = Location::query()
            ->where(function ($query) {
                $query->whereNull('user_id')
                    ->orWhere('user_id', Auth::id());
            })
            ->orderBy('name')
            ->get();

        return view('pages.user.map', compact('locations'));
    }
}
