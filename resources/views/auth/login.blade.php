<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-black text-zinc-900 tracking-tight leading-none mb-2">Bon retour !</h2>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Connectez-vous à votre espace</p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">E-mail</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all placeholder:text-zinc-300"
                   placeholder="votre@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <div class="flex items-center justify-between mb-1.5 ml-1">
                <label for="password" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest text-zinc-400">Mot de passe</label>
                @if (Route::has('password.request'))
                    <a class="text-[10px] font-black text-brand-600 hover:text-brand-700 uppercase tracking-widest" href="{{ route('password.request') }}">
                        Oublié ?
                    </a>
                @endif
            </div>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Remember Me -->
        <div class="flex items-center ml-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" class="w-5 h-5 rounded-lg border-zinc-200 text-brand-600 shadow-sm focus:ring-brand-500 transition-all cursor-pointer" name="remember">
                <span class="ms-3 text-xs font-bold text-zinc-500 group-hover:text-zinc-800 transition-colors uppercase tracking-widest">Rester connecté</span>
            </label>
        </div>

        <button type="submit" class="w-full py-5 bg-brand-600 text-white font-black rounded-[1.5rem] hover:bg-brand-700 transition-all shadow-xl shadow-brand-100 hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-sm">
            Connexion
        </button>

        @if (Route::has('register'))
            <p class="text-center text-xs font-bold text-zinc-400 mt-8">
                PAS ENCORE DE COMPTE ? 
                <a href="{{ route('register') }}" class="text-brand-600 hover:text-brand-700 ml-1">S'INSCRIRE</a>
            </p>
        @endif
    </form>
</x-guest-layout>
