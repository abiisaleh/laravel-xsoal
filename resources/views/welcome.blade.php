<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />

    <!-- Styles -->
    @vite('resources/css/app.css')
</head>

<body class="antialiased bg-gray-100">
    <nav class="flex justify-between py-4 bg-white shadow-md m-4 rounded-md px-8 items-center">
        <a href="/" class="text-lg font-bold block">{{ config('app.name') }}</a>
        <a href="/app" class=" text-white px-8 py-2 bg-black block font-semibold transition-all rounded">Login</a>
    </nav>

    <main>
        <section id="home" class="flex justify-center min-h-[79vh]">
            <div class="text-center max-w-screen-sm mx-8 mt-8">
                <img src="img/woman-technologist_1f469-200d-1f4bb.webp" alt="emoji" width="200"
                    class="mx-auto mb-8">
                <h1 class="text-4xl font-bold mb-4">Bikin Soal Ulangan Sekolah? Gampang Banget!</h1>
                <p class="mb-8">Tanpa Repot, Tanpa Edit Manual - Solusi Terbaik untuk para Guru</p>

                <div class="flex justify-center">
                    <a href="/app/register"
                        class="border-2 border-black px-8 py-2 bg-white block font-semibold btn-shadow transition-all">Coba
                        Sekarang</a>
                </div>
            </div>

        </section>
    </main>

    <footer class="text-center py-4">
        <p class="text-sm">made by <a href="https://abiisaleh.xyz" class="hover:underline">abiisaleh</a> with ❤</p>
    </footer>
</body>

</html>
