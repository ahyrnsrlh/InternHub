<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk | InternHub</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#e9e9e9] px-4 py-8 text-gray-900" style="font-family: 'Manrope', sans-serif;">
    <div class="mx-auto flex min-h-[calc(100vh-4rem)] w-full max-w-6xl items-center justify-center">
        <div class="grid w-full overflow-hidden rounded-[2rem] border border-gray-200 bg-white shadow-xl lg:grid-cols-2">
            <section class="px-6 py-8 sm:px-10 sm:py-10 lg:px-12 lg:py-12">
                <div class="mx-auto w-full max-w-md">
                    <img src="{{ asset('logo-internhub.png') }}" alt="Logo InternHub" class="h-10 w-auto object-contain">
                    <h1 class="mt-6 text-4xl font-extrabold leading-tight tracking-tight text-gray-900">Selamat datang kembali!</h1>
                    <p class="mt-3 text-sm text-gray-500">Sederhanakan alur kerja magang Anda dan lanjutkan aktivitas harian dengan lebih produktif.</p>

                    @if (session('success'))
                        <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-medium text-emerald-700">
                            {{ session('success') }}
                        </div>
                    @endif

                    <x-auth-session-status class="mt-5" :status="session('status')" />

                    <form method="POST" action="{{ route('login') }}" class="mt-7 space-y-4">
                        @csrf

                        <div>
                            <label for="email" class="mb-1.5 block text-sm font-medium text-gray-700">Email</label>
                            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="nama@instansi.com" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <label for="password" class="mb-1.5 block text-sm font-medium text-gray-700">Kata Sandi</label>
                            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" class="w-full rounded-full border border-gray-300 px-4 py-3 text-sm outline-none transition focus:border-gray-900 focus:ring-2 focus:ring-gray-200">
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div class="flex items-center justify-between text-sm">
                            <label class="inline-flex items-center gap-2 text-gray-600">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-black focus:ring-gray-400">
                                <span>Ingat saya</span>
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="font-medium text-gray-700 hover:text-black">Lupa kata sandi?</a>
                            @endif
                        </div>

                        <button type="submit" class="w-full rounded-full bg-blue-600 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-700">Masuk</button>
                    </form>

                    <div class="my-7 flex items-center gap-3 text-xs text-gray-400">
                        <span class="h-px flex-1 bg-gray-200"></span>
                        <span>atau lanjutkan dengan</span>
                        <span class="h-px flex-1 bg-gray-200"></span>
                    </div>

                    <div class="flex items-center justify-center gap-4">
                        <button type="button" class="grid h-11 w-11 place-content-center rounded-full border border-gray-300 text-sm font-bold text-gray-700">G</button>
                        <button type="button" class="grid h-11 w-11 place-content-center rounded-full border border-gray-300 text-sm font-bold text-gray-700">A</button>
                        <button type="button" class="grid h-11 w-11 place-content-center rounded-full border border-gray-300 text-sm font-bold text-gray-700">f</button>
                    </div>

                    <p class="mt-8 text-center text-sm text-gray-500">Belum punya akun? <a href="{{ route('internhub.register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Daftar sekarang</a></p>
                </div>
            </section>

            <section class="hidden bg-[#eef3ee] p-8 lg:block xl:p-10">
                <div class="h-full rounded-3xl border border-[#dfe8df] bg-[#edf3ed] p-5">
                    <img src="{{ asset('illustrasi.png') }}" alt="Ilustrasi InternHub" class="h-full w-full rounded-2xl object-cover">
                </div>
            </section>
        </div>
    </div>
</body>
</html>
