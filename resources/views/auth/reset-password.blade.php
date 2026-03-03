<x-guest-layout>
    <div class="mb-8">
        <h2 class="text-2xl font-black text-zinc-900 tracking-tight leading-none mb-2">Nouveau départ</h2>
        <p class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Réinitialisez votre mot de passe</p>
    </div>

    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf

        <!-- Password Reset Token -->
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">E-mail</label>
            <input id="email" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username"
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all placeholder:text-zinc-300">
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Nouveau mot de passe</label>
            <input id="password" type="password" name="password" required autocomplete="new-password"
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div>
            <label for="password_confirmation" class="block text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1.5 ml-1">Confirmer</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password"
                   class="w-full bg-zinc-50 border-zinc-200 rounded-2xl px-5 py-4 text-sm font-medium focus:ring-2 focus:ring-brand-500 focus:border-brand-500 transition-all">
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <button type="submit" class="w-full py-5 bg-brand-600 text-white font-black rounded-[1.5rem] hover:bg-brand-700 transition-all shadow-xl shadow-brand-100 hover:-translate-y-1 active:scale-95 uppercase tracking-widest text-sm mt-4">
            Réinitialiser
        </button>
    </form>
</x-guest-layout>
