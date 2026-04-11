@extends('layouts.user')

@section('title', 'Attendance')
@section('header', 'GPS Attendance')

@section('content')
<div class="space-y-6" x-data="attendanceForm()">
    @if (session('status'))
        <div class="rounded-xl border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-700">
            {{ session('status') }}
        </div>
    @endif

    @if ($errors->has('attendance'))
        <div class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
            {{ $errors->first('attendance') }}
        </div>
    @endif

    <x-card>
        <div class="grid gap-4 lg:grid-cols-2">
            <form method="POST" action="{{ route('user.attendance.check-in') }}" class="space-y-3">
                @csrf
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Check In</h3>
                    <p class="mt-1 text-sm text-gray-500">Record your check in with GPS validation.</p>
                </div>

                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Location</label>
                    <select name="location_id" class="w-full rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-800 shadow-sm outline-none transition focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        <option value="">Select location</option>
                        @foreach ($locations as $location)
                            <option value="{{ $location->id }}" @selected(old('location_id') == $location->id)>
                                {{ $location->name }} - {{ $location->address }}
                            </option>
                        @endforeach
                    </select>
                    @error('location_id')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <input type="hidden" name="latitude" x-model="lat">
                <input type="hidden" name="longitude" x-model="lng">
                <input type="hidden" name="allowed_radius_meters" value="10">

                @error('latitude')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror
                @error('longitude')
                    <p class="text-xs text-red-500">{{ $message }}</p>
                @enderror

                <x-button type="submit" class="w-full justify-center" x-on:click="getGps()">Check In</x-button>
            </form>

            <form method="POST" action="{{ route('user.attendance.check-out') }}" class="space-y-3">
                @csrf
                @method('PATCH')
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Check Out</h3>
                    <p class="mt-1 text-sm text-gray-500">Close your active attendance session.</p>
                </div>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-3 text-sm text-gray-600">
                    @if ($activeAttendance)
                        Active check-in at: {{ optional($activeAttendance->check_in_time)->format('d M Y H:i') }}
                    @else
                        No active check-in.
                    @endif
                </div>

                <x-button type="submit" variant="secondary" class="w-full justify-center" @disabled(! $activeAttendance)>Check Out</x-button>
            </form>
        </div>
    </x-card>

    <div class="grid gap-4 lg:grid-cols-3">
        <x-card class="lg:col-span-2" title="Attendance History">
            @if ($attendances->count())
                <div class="overflow-hidden rounded-xl border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200 text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Check In</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Check Out</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Coordinates</th>
                                <th class="px-4 py-3 text-left font-semibold text-gray-600">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($attendances as $attendance)
                                <tr>
                                    <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_in_time)->format('d M Y H:i') }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ optional($attendance->check_out_time)->format('d M Y H:i') ?? '-' }}</td>
                                    <td class="px-4 py-3 text-gray-700">{{ $attendance->latitude }}, {{ $attendance->longitude }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold {{ $attendance->status === 'valid' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-600' }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $attendances->links() }}</div>
            @else
                <div class="rounded-xl border border-dashed border-gray-300 bg-gray-50 p-8 text-center text-sm text-gray-500">
                    No attendance data yet.
                </div>
            @endif
        </x-card>

        <x-card>
            <h3 class="text-base font-semibold text-gray-900">GPS Status</h3>
            <dl class="mt-4 space-y-3 text-sm">
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Latitude</dt>
                    <dd class="font-medium text-gray-900" x-text="lat"></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">Longitude</dt>
                    <dd class="font-medium text-gray-900" x-text="lng"></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-gray-500">State</dt>
                    <dd class="font-medium text-gray-900" x-text="gpsReady ? 'Ready' : 'Waiting GPS'"></dd>
                </div>
            </dl>
        </x-card>
    </div>
</div>
@endsection

@push('scripts')
<script>
function attendanceForm() {
    return {
        lat: "{{ old('latitude', '-') }}",
        lng: "{{ old('longitude', '-') }}",
        gpsReady: false,
        getGps() {
            if (!navigator.geolocation) {
                return;
            }

            navigator.geolocation.getCurrentPosition((position) => {
                this.lat = position.coords.latitude.toFixed(7);
                this.lng = position.coords.longitude.toFixed(7);
                this.gpsReady = true;
            });
        }
    }
}
</script>
@endpush
