<?php

namespace App\Http\Controllers\User;

use App\Models\Location;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\LocationRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(): View
    {
        $locations = Location::query()->latest()->paginate(10);

        return view('pages.user.locations', compact('locations'));
    }

    public function create(): View
    {
        return view('pages.user.locations');
    }

    public function store(LocationRequest $request): RedirectResponse
    {
        Location::query()->create($request->validated());

        return redirect()->route('user.locations.index')->with('status', 'Location saved successfully.');
    }

    public function edit(string $location): View
    {
        $locationData = Location::query()->findOrFail($location);

        return view('pages.user.locations', compact('locationData'));
    }

    public function update(LocationRequest $request, string $location): RedirectResponse
    {
        $locationData = Location::query()->findOrFail($location);
        $locationData->update($request->validated());

        return redirect()->route('user.locations.index')->with('status', 'Location updated successfully.');
    }

    public function destroy(string $location): RedirectResponse
    {
        $locationData = Location::query()->findOrFail($location);
        $locationData->delete();

        return redirect()->route('user.locations.index')->with('status', 'Location deleted successfully.');
    }

    public function map(): View
    {
        $locations = Location::query()->where('is_active', true)->get();

        return view('pages.user.map', compact('locations'));
    }
}
