@extends('layouts.admin')

@section('title', 'Manajemen Peserta Magang')
@section('header', 'Peserta Magang')

@section('content')
@php
    $internPayload = $interns->getCollection()->map(function ($intern) {
        return [
            'id' => (int) $intern->id,
            'name' => $intern->name,
            'email' => $intern->email,
            'placement' => $intern->placement,
            'status' => $intern->status === 'inactive' ? 'inactive' : 'active',
        ];
    })->values();

    $updateRouteTemplate = route('internhub.admin.interns.update', ['internUser' => '__ID__']);
    $deleteRouteTemplate = route('internhub.admin.interns.destroy', ['internUser' => '__ID__']);
@endphp

<script type="application/json" id="admin-interns-json">@json($internPayload)</script>
<script type="application/json" id="admin-interns-route-templates">@json(['update' => $updateRouteTemplate, 'delete' => $deleteRouteTemplate])</script>

<div class="space-y-6" x-data="adminInternManager()" x-init="init()">
    @if (session('status'))
        <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">{{ session('status') }}</div>
    @endif

    @if (session('error'))
        <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">{{ session('error') }}</div>
    @endif

    <x-card>
        <form method="GET" action="{{ route('internhub.admin.interns') }}" class="flex flex-wrap items-center gap-3">
            <div class="min-w-56 flex-1">
                <x-input name="search" value="{{ $search }}" placeholder="Cari nama peserta atau instansi..." />
            </div>
            <select name="status" class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                <option value="" @selected($status === '')>Semua Status</option>
                <option value="active" @selected($status === 'active')>Aktif</option>
                <option value="inactive" @selected($status === 'inactive')>Nonaktif</option>
            </select>
            <x-button type="submit" variant="secondary">Filter</x-button>
            <x-button type="button" x-on:click="$dispatch('open-modal', 'add-intern')">Tambah Peserta</x-button>
        </form>
    </x-card>

    <x-table :headers="['Nama', 'Instansi', 'Status', 'Aksi']" x-show="!empty">
        @foreach ($interns as $intern)
            <tr class="bg-white">
                <td class="px-4 py-3">
                    <p class="font-medium text-gray-900">{{ $intern->name }}</p>
                    <p class="text-xs text-gray-500">{{ $intern->email }}</p>
                </td>
                <td class="px-4 py-3 text-gray-700">{{ $intern->placement ?: '-' }}</td>
                <td class="px-4 py-3">
                    @if ($intern->status === 'active')
                        <x-badge variant="success">Aktif</x-badge>
                    @else
                        <x-badge variant="warning">Nonaktif</x-badge>
                    @endif
                </td>
                <td class="px-4 py-3">
                    <div class="flex gap-2">
                        <x-button type="button" variant="secondary" class="px-3 py-2" x-on:click="openEditById({{ $intern->id }})">Ubah</x-button>
                        <x-button type="button" variant="danger" class="px-3 py-2" x-on:click="openDeleteById({{ $intern->id }})">Hapus</x-button>
                    </div>
                </td>
            </tr>
        @endforeach
    </x-table>

    <div class="rounded-xl border border-dashed border-gray-300 bg-white p-10 text-center" x-show="empty">
        <p class="text-sm text-gray-500">Data peserta belum tersedia. Tambahkan peserta pertama Anda.</p>
    </div>

    <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl border border-gray-200 bg-white px-4 py-3 text-sm" x-show="!empty">
        <p class="text-gray-500">Menampilkan {{ $interns->firstItem() ?? 0 }}-{{ $interns->lastItem() ?? 0 }} dari {{ $interns->total() }} peserta</p>
        <div>{{ $interns->links() }}</div>
    </div>

    <x-modal name="add-intern" maxWidth="lg">
        <form method="POST" action="{{ route('internhub.admin.interns.store') }}" class="p-6" x-data>
            @csrf
            <h3 class="text-lg font-semibold text-gray-900">Tambah Peserta Magang</h3>
            <div class="mt-4 grid gap-3">
                <x-input name="name" placeholder="Nama lengkap" required />
                <x-input name="email" type="email" placeholder="Email" required />
                <x-input name="placement" placeholder="Instansi" required />
                <x-input name="password" type="password" placeholder="Password minimal 8 karakter" required />
                <select name="status" class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button variant="secondary" x-on:click="$dispatch('close-modal', 'add-intern')">Batal</x-button>
                <x-button type="submit">Simpan</x-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="edit-intern" maxWidth="lg">
        <form method="POST" x-bind:action="editFormAction" class="p-6">
            @csrf
            @method('PUT')
            <h3 class="text-lg font-semibold text-gray-900">Ubah Peserta Magang</h3>
            <div class="mt-4 grid gap-3">
                <x-input name="name" x-model="editForm.name" placeholder="Nama lengkap" required />
                <x-input name="email" type="email" x-model="editForm.email" placeholder="Email" required />
                <x-input name="placement" x-model="editForm.placement" placeholder="Instansi" required />
                <x-input name="password" type="password" placeholder="Kosongkan jika tidak ingin mengubah password" />
                <select name="status" x-model="editForm.status" class="rounded-xl border border-gray-200 bg-white px-3.5 py-2.5 text-sm text-gray-700 shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-100">
                    <option value="active">Aktif</option>
                    <option value="inactive">Nonaktif</option>
                </select>
            </div>
            <div class="mt-6 flex justify-end gap-2">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'edit-intern')">Batal</x-button>
                <x-button type="submit">Simpan Perubahan</x-button>
            </div>
        </form>
    </x-modal>

    <x-modal name="delete-intern" maxWidth="md">
        <form method="POST" x-bind:action="deleteFormAction" class="p-6">
            @csrf
            @method('DELETE')
            <h3 class="text-lg font-semibold text-gray-900">Hapus Peserta Magang</h3>
            <p class="mt-2 text-sm text-gray-500">Tindakan ini tidak dapat dibatalkan. Yakin ingin menghapus <span class="font-medium text-gray-700" x-text="deleteForm.name"></span>?</p>
            <div class="mt-6 flex justify-end gap-2">
                <x-button type="button" variant="secondary" x-on:click="$dispatch('close-modal', 'delete-intern')">Batal</x-button>
                <x-button type="submit" variant="danger">Hapus</x-button>
            </div>
        </form>
    </x-modal>
</div>

@push('scripts')
<script>
    function adminInternManager() {
        return {
            locations: [],
            empty: false,
            editForm: {
                id: null,
                name: '',
                email: '',
                placement: '',
                status: 'active',
            },
            deleteForm: {
                id: null,
                name: '',
            },
            routeTemplates: {
                update: '',
                delete: '',
            },
            get editFormAction() {
                if (!this.editForm.id || !this.routeTemplates.update) {
                    return '#';
                }

                return this.routeTemplates.update.replace('__ID__', this.editForm.id);
            },
            get deleteFormAction() {
                if (!this.deleteForm.id || !this.routeTemplates.delete) {
                    return '#';
                }

                return this.routeTemplates.delete.replace('__ID__', this.deleteForm.id);
            },
            init() {
                const payloadElement = document.getElementById('admin-interns-json');
                const routeTemplateElement = document.getElementById('admin-interns-route-templates');

                if (!payloadElement || !routeTemplateElement) {
                    this.empty = true;
                    return;
                }

                try {
                    this.locations = JSON.parse(payloadElement.textContent || '[]');
                } catch (error) {
                    this.locations = [];
                }

                try {
                    this.routeTemplates = JSON.parse(routeTemplateElement.textContent || '{}');
                } catch (error) {
                    this.routeTemplates = { update: '', delete: '' };
                }

                this.empty = this.locations.length === 0;
            },
            openEditById(internId) {
                const intern = this.locations.find((item) => Number(item.id) === Number(internId));
                if (!intern) {
                    return;
                }

                this.editForm = { ...intern };
                this.$dispatch('open-modal', 'edit-intern');
            },
            openDeleteById(internId) {
                const intern = this.locations.find((item) => Number(item.id) === Number(internId));
                if (!intern) {
                    return;
                }

                this.deleteForm = {
                    id: intern.id,
                    name: intern.name,
                };
                this.$dispatch('open-modal', 'delete-intern');
            },
        };
    }
</script>
@endpush
@endsection
