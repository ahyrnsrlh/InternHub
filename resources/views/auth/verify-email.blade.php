<x-guest-layout>
    <div class="mb-4 text-sm text-content-muted">
        Terima kasih telah mendaftar. Sebelum melanjutkan, silakan verifikasi alamat email Anda melalui tautan yang baru kami kirimkan. Jika email belum diterima, kami akan mengirimkan ulang.
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-4 font-medium text-sm text-green-600">
            Tautan verifikasi baru telah dikirim ke alamat email yang Anda daftarkan.
        </div>
    @endif

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('verification.send') }}">
            @csrf

            <div>
                <x-primary-button>
                    Kirim Ulang Email Verifikasi
                </x-primary-button>
            </div>
        </form>

        <form method="POST" action="{{ route('logout') }}">
            @csrf

            <button type="submit" class="underline text-sm text-content-muted hover:text-content rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                Keluar
            </button>
        </form>
    </div>
</x-guest-layout>
