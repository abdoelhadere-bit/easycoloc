<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>EasyColoc - La colocation intelligente</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap" rel="stylesheet">

        <!-- Styles / Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="antialiased bg-zinc-50 text-zinc-900 font-sans selection:bg-brand-500 selection:text-white">
        <div class="relative min-h-screen flex flex-col">
            {{-- Navigation --}}
            <nav class="relative z-10 max-w-7xl mx-auto w-full px-6 py-8 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="w-10 h-10 bg-brand-600 rounded-xl shadow-lg shadow-brand-200 flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    </div>
                    <span class="text-xl font-black tracking-tighter uppercase italic">Easy<span class="text-brand-600">Coloc</span></span>
                </div>

                @if (Route::has('login'))
                    <div class="flex items-center gap-4">
                        @auth
                            <a href="{{ url('/dashboard') }}" class="px-5 py-2.5 bg-zinc-900 text-white text-sm font-bold rounded-xl hover:bg-zinc-800 transition-all shadow-lg shadow-zinc-200">Tableau de bord</a>
                        @else
                            <a href="{{ route('login') }}" class="text-sm font-bold text-zinc-600 hover:text-zinc-900 transition-colors">Connexion</a>
                            @if (Route::has('register'))
                                <a href="{{ route('register') }}" class="px-5 py-2.5 bg-brand-600 text-white text-sm font-bold rounded-xl hover:bg-brand-700 transition-all shadow-lg shadow-brand-100">Commencer</a>
                            @endif
                        @endauth
                    </div>
                @endif
            </nav>

            {{-- Hero Section --}}
            <main class="relative flex-1 flex items-center">
                <div class="max-w-7xl mx-auto px-6 w-full grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                    <div class="space-y-8 animate-fade-in-up">
                        <div class="inline-flex items-center gap-2 px-3 py-1 bg-brand-50 border border-brand-100 rounded-full">
                            <span class="flex h-2 w-2 rounded-full bg-brand-600"></span>
                            <span class="text-[10px] font-black text-brand-700 uppercase tracking-widest">Nouveau : Gestion automatisée</span>
                        </div>
                        
                        <h1 class="text-5xl lg:text-7xl font-black text-zinc-900 tracking-tight leading-[0.9]">
                            Gérez votre colocation <br>
                            <span class="text-brand-600 italic">sans le stress.</span>
                        </h1>
                        
                        <p class="text-lg text-zinc-500 max-w-md font-medium leading-relaxed">
                            Finis les tableaux Excel compliqués. EasyColoc simplifie vos comptes, vos dépenses et vos remboursements en un clin d'œil.
                        </p>

                        <div class="flex flex-wrap items-center gap-4">
                            <a href="{{ route('register') }}" class="px-8 py-4 bg-brand-600 text-white font-black rounded-2xl hover:bg-brand-700 transition-all shadow-2xl shadow-brand-200 hover:-translate-y-1 active:scale-95">
                                CRÉER MON COMPTE
                            </a>
                            <div class="flex -space-x-3 items-center">
                                <div class="w-10 h-10 rounded-full border-4 border-white bg-zinc-200"></div>
                                <div class="w-10 h-10 rounded-full border-4 border-white bg-brand-100 flex items-center justify-center text-[10px] font-bold">+500</div>
                                <span class="ml-4 text-xs font-bold text-zinc-400 italic">utilisateurs nous font confiance</span>
                            </div>
                        </div>
                    </div>

                    <div class="relative hidden lg:block animate-fade-in">
                        <div class="absolute -inset-4 bg-brand-500/10 blur-3xl rounded-full"></div>
                        <div class="relative bg-white border border-zinc-200 rounded-[2.5rem] shadow-2xl p-8 transform rotate-2 hover:rotate-0 transition-transform duration-700 overflow-hidden">
                            <div class="flex items-center justify-between mb-8">
                                <h3 class="font-black text-xl italic uppercase tracking-tighter text-zinc-400">Dépenses</h3>
                                <span class="px-2 py-1 bg-emerald-50 text-emerald-600 text-[10px] font-black rounded-lg">A JOUR</span>
                            </div>
                            <div class="space-y-4">
                                @foreach(['Courses', 'Internet', 'Électricité'] as $item)
                                    <div class="flex items-center justify-between p-4 bg-zinc-50 rounded-2xl border border-zinc-100">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 bg-white rounded-xl shadow-sm border border-zinc-200 flex items-center justify-center">
                                                <svg class="w-5 h-5 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                            </div>
                                            <div>
                                                <p class="font-bold text-sm text-zinc-900">{{ $item }}</p>
                                                <p class="text-[10px] text-zinc-400 font-bold uppercase tracking-widest">Aujourd'hui</p>
                                            </div>
                                        </div>
                                        <span class="font-black text-zinc-900">45,00 DH</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            {{-- Footer --}}
            <footer class="p-8 text-center bg-white border-t border-zinc-100">
                <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">&copy; {{ date('Y') }} EasyColoc. Crafted for shared living.</p>
            </footer>
        </div>
    </body>
</html>
