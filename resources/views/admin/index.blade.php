<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Supervision Globale</h2>
    </x-slot>

    <div class="space-y-8 pb-12">
        {{-- Status Messages --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 shadow-sm animate-fade-in">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span class="font-medium text-sm">{{ $errors->first() }}</span>
            </div>
        @endif

        {{-- Stats Grid --}}
        <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
            <div class="p-6 bg-white rounded-3xl border border-zinc-200 shadow-sm transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Utilisateurs</p>
                <p class="text-2xl font-black text-zinc-900">{{ $stats['users'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-3xl border border-zinc-200 shadow-sm transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Colocations</p>
                <p class="text-2xl font-black text-zinc-900">{{ $stats['colocations'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-3xl border border-zinc-200 shadow-sm transition-transform hover:-translate-y-1 lg:col-span-1 col-span-2">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Flux Total</p>
                <p class="text-2xl font-black text-brand-600">{{ number_format($stats['expenses_total'], 0) }} DH</p>
            </div>
            <div class="p-6 bg-white rounded-3xl border border-zinc-200 shadow-sm transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-zinc-400 uppercase tracking-widest mb-1">Paiements</p>
                <p class="text-2xl font-black text-emerald-600">{{ $stats['payments'] }}</p>
            </div>
            <div class="p-6 bg-white rounded-3xl border border-zinc-200 shadow-sm transition-transform hover:-translate-y-1">
                <p class="text-[10px] font-black text-rose-500 uppercase tracking-widest mb-1">Signalés</p>
                <p class="text-2xl font-black text-rose-600">{{ $stats['banned'] }}</p>
            </div>
        </div>

        {{-- USER MANAGEMENT TABLE --}}
        <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-zinc-100 flex items-center justify-between">
                <h3 class="font-bold text-zinc-900">Gestion des utilisateurs</h3>
                <div class="relative">
                    <input type="text" placeholder="Rechercher..." class="bg-zinc-50 border-zinc-200 rounded-xl px-4 py-2 text-xs font-medium focus:ring-brand-500 min-w-[200px]">
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-zinc-50 border-b border-zinc-100">
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-widest">Utilisateur</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-widest">Role</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-widest text-center">Réputation</th>
                            <th class="px-6 py-4 text-left text-xs font-bold text-zinc-400 uppercase tracking-widest">Statut</th>
                            <th class="px-6 py-4 text-right text-xs font-bold text-zinc-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100">
                        @foreach($users as $u)
                            <tr class="hover:bg-zinc-50/50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center text-brand-700 font-bold text-xs">
                                            {{ substr($u->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-zinc-900 leading-none mb-1">{{ $u->name }}</div>
                                            <div class="text-[10px] text-zinc-400 font-medium">{{ $u->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-600">
                                    {{ $u->role }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="px-2 py-1 {{ $u->reputation >= 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-100' : 'bg-rose-50 text-rose-700 border-rose-100' }} border rounded text-[10px] font-black">
                                        {{ $u->reputation }} pts
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($u->is_banned)
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-rose-100 text-rose-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-rose-600"></span>
                                            BANNI
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 py-1 px-2.5 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-600"></span>
                                            ACTIF
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if(!$u->is_banned)
                                        <form method="POST" action="{{ route('admin.users.ban', $u) }}" class="inline">
                                            @csrf
                                            <button class="px-4 py-2 bg-rose-600 text-white text-[10px] font-black rounded-lg hover:bg-rose-700 transition-all shadow-lg shadow-rose-100">BANNIRE</button>
                                        </form>
                                    @else
                                        <form method="POST" action="{{ route('admin.users.unban', $u) }}" class="inline">
                                            @csrf
                                            <button class="px-4 py-2 bg-zinc-900 text-white text-[10px] font-black rounded-lg hover:bg-zinc-800 transition-all">RÉACTIVER</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="p-6 bg-zinc-50 border-t border-zinc-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>