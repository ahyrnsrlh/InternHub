<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'InternHub') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@200..800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-canvas text-content">
    <main class="mx-auto flex min-h-screen w-full max-w-5xl flex-col items-center justify-center px-6 py-16">
        <div class="w-full rounded-3xl border border-line bg-surface p-10 shadow-xl shadow-content/5">
            <p class="inline-flex rounded-full bg-primary-soft px-4 py-1 text-xs font-bold uppercase tracking-widest text-content-muted">InternHub</p>
            <h1 class="mt-6 text-4xl font-black tracking-tight text-content sm:text-5xl">Operasional magang modern dalam satu ruang kerja.</h1>
            <p class="mt-4 max-w-2xl text-base text-content-muted sm:text-lg">Portal ini menggunakan sistem desain warna semantik, siap mode gelap, dan terhubung ke modul beranda, presensi, catatan harian, evaluasi, serta pusat admin.</p>

            <div class="mt-10 flex flex-wrap items-center gap-3">
                @auth
                    <a href="{{ url('/dashboard') }}" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground hover:bg-primary-hover">Buka Beranda</a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center rounded-xl bg-primary px-5 py-2.5 text-sm font-semibold text-primary-foreground hover:bg-primary-hover">Masuk</a>
                    @if (Route::has('register'))
                        <a href="{{ route('register') }}" class="inline-flex items-center rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-content hover:bg-surface-muted">Buat Akun</a>
                    @endif
                @endauth
                <a href="{{ route('internhub.dashboard') }}" class="inline-flex items-center rounded-xl border border-line px-5 py-2.5 text-sm font-semibold text-content hover:bg-surface-muted">Pratinjau InternHub</a>
            </div>
        </div>
    </main>
</body>
</html>