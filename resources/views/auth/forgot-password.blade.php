<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-black text-zinc-900 tracking-tight leading-none mb-2">Oubli ?</h2>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Récupérez votre accès rapidement</p>
    </div>

    <div class="mb-6 text-sm font-medium text-zinc-500 leading-relaxed">
        {{ __('Entrez votre adresse e-mail et nous vous enverrons un lien de réinitialisation pour choisir un nouveau mot de passe.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">E-mail</label>
            <input id="email" type="email" name="email" :value="old('email')" required autofocus
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all placeholder:text-zinc-300"
                   placeholder="votre@email.com">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-5 bg-brand-600 text-white font-black rounded-[1.5rem] hover:bg-brand-700 transition-all shadow-xl shadow-brand-100 hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-sm">
            Envoyer le lien
        </button>

        <p class="text-center text-xs font-bold text-zinc-400 mt-8">
            RETOUR À LA 
            <a href="{{ route('login') }}" class="text-brand-600 hover:text-brand-700 ml-1 uppercase tracking-widest">CONNEXION</a>
        </p>
    </form>
</x-guest-layout>
