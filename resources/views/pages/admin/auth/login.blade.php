<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-gray-50 text-gray-800" style="font-family: 'Manrope', sans-serif;">
    <div class="grid min-h-screen place-items-center px-4">
        <div class="w-full max-w-md rounded-2xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            <div class="mb-6 text-center">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-indigo-600">InternHub Admin</p>
                <h1 class="mt-2 text-2xl font-bold text-gray-900">Masuk sebagai administrator</h1>
                <p class="mt-1 text-sm text-gray-500">Kelola peserta magang, presensi, dan laporan dengan aman.</p>
            </div>

            <form class="space-y-4">
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Email</label>
                    <x-input type="email" placeholder="admin@internhub.test" />
                </div>
                <div>
                    <label class="mb-1 block text-sm font-medium text-gray-700">Kata Sandi</label>
                    <x-input type="password" placeholder="Masukkan kata sandi" />
                </div>
                <x-button type="button" class="w-full justify-center">Masuk</x-button>
            </form>
        </div>
    </div>
</body>
</html>
