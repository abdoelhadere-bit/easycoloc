<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyColoc') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-zinc-900 antialiased bg-zinc-50 selection:bg-brand-500 selection:text-white">
        <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
            {{-- Background Accent --}}
            <div class="absolute top-0 left-0 w-full h-1 bg-brand-600"></div>
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-brand-500/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -right-24 w-96 h-96 bg-brand-500/5 rounded-full blur-3xl"></div>

            <div class="w-full max-w-md relative z-10">
                <div class="flex flex-col items-center mb-10">
                    <a href="/" class="group">
                        <div class="w-16 h-16 bg-brand-600 rounded-2xl shadow-xl shadow-brand-200 flex items-center justify-center transition-transform group-hover:scale-110 duration-500">
                             <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                        </div>
                    </a>
                    <h1 class="mt-6 text-2xl font-black tracking-tighter uppercase italic">Easy<span class="text-brand-600">Coloc</span></h1>
                </div>

                <div class="bg-white p-8 sm:p-10 rounded-[2.5rem] border border-zinc-200 shadow-2xl shadow-zinc-200/50">
                    {{ $slot }}
                </div>

                <div class="mt-8 text-center">
                    <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest">&copy; {{ date('Y') }} EasyColoc. Simple & Shared.</p>
                </div>
            </div>
        </div>
    </body>
</html>
