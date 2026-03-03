<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Tableau de bord</h2>

            <a href="{{ route('colocations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition-all active:scale-95">
                + Nouvelle colocation
            </a>
        </div>
    </x-slot>

    <div class="space-y-8">
        {{-- Alerts --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 shadow-sm">
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Top cards --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-3xl border border-zinc-200 p-6 shadow-sm">
                <div class="text-sm text-zinc-500 mb-2">Mon score réputation</div>
                <div class="text-3xl font-black text-zinc-900">{{ $reputation }}</div>
            </div>

            <div class="bg-white rounded-3xl border border-zinc-200 p-6 shadow-sm">
                <div class="text-sm text-zinc-500 mb-2">Dépenses Globales ({{ now()->format('M') }})</div>
                <div class="text-3xl font-black text-zinc-900">{{ number_format($monthlyTotal, 2) }} DH</div>
            </div>
        </div>

        {{-- Main grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Left --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Statut actuel --}}
                <div class="bg-white rounded-3xl border border-zinc-200 p-8 shadow-sm">
                    <h3 class="text-xs font-bold text-brand-600 uppercase tracking-widest mb-2">Statut actuel</h3>

                    @if($activeColocation)
                        <h2 class="text-3xl font-black text-zinc-900 mb-2">{{ $activeColocation->name }}</h2>
                        <p class="text-zinc-500 mb-6 max-w-md text-lg">
                            Vous êtes membre d'une colocation active. Gérez vos dépenses et vos colocataires en un clic.
                        </p>

                        <a href="{{ route('colocations.show', $activeColocation) }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-brand-600 text-white rounded-xl font-bold shadow-lg shadow-brand-200 hover:bg-brand-700 transition-all active:scale-95">
                            Ouvrir l'espace →
                        </a>
                    @else
                        <h2 class="text-3xl font-black text-zinc-900 mb-2">Aucune colocation</h2>
                        <p class="text-zinc-500 mb-6 max-w-md text-lg">
                            Vous n'avez pas encore rejoint de colocation. Créez-en une dès maintenant pour commencer.
                        </p>

                        <a href="{{ route('colocations.create') }}"
                           class="inline-flex items-center gap-2 px-6 py-3 bg-zinc-900 text-white rounded-xl font-bold hover:bg-zinc-800 transition-all active:scale-95">
                            Créer une colocation
                        </a>
                    @endif
                </div>

                {{-- Dépenses récentes --}}
                <div class="bg-white rounded-3xl border border-zinc-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-zinc-900">Dépenses récentes</h3>
                        
                    </div>

                    @if($recentExpenses->isEmpty())
                        <div class="py-10 text-center text-zinc-500">Aucune dépense.</div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full text-sm">
                                <thead class="text-zinc-400">
                                    <tr class="border-b">
                                        <th class="text-left py-3">Titre / Catégorie</th>
                                        <th class="text-left py-3">Payeur</th>
                                        <th class="text-right py-3">Montant</th>
                                        <th class="text-right py-3">Coloc</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentExpenses as $e)
                                        <tr class="border-b last:border-0">
                                            <td class="py-3">
                                                <div class="font-semibold text-zinc-900">{{ $e->title }}</div>
                                                <div class="text-xs text-zinc-400">{{ $e->category?->name ?? '—' }}</div>
                                            </td>
                                            <td class="py-3 text-zinc-700 font-semibold">{{ $e->payer->name }}</td>
                                            <td class="py-3 text-right font-black">{{ number_format($e->amount, 2) }} DH</td>
                                            <td class="py-3 text-right text-zinc-500">{{ $e->colocation->name ?? '—' }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Right --}}
            <div class="space-y-6">

                {{-- Invitations --}}
                <div class="bg-white rounded-3xl border border-zinc-200 p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-zinc-900">Invitations reçues</h3>
                        <span class="px-2.5 py-1 bg-zinc-100 text-zinc-600 text-xs font-bold rounded-full">
                            {{ $pendingInvitations->count() }}
                        </span>
                    </div>

                    @if($pendingInvitations->isEmpty())
                        <div class="py-10 text-center text-zinc-500 text-sm">Rien à voir ici pour l'instant !</div>
                    @else
                        <div class="space-y-3">
                            @foreach($pendingInvitations as $inv)
                                <div class="p-4 bg-zinc-50 rounded-2xl border border-zinc-100">
                                    <div class="font-bold text-zinc-900">{{ $inv->colocation->name }}</div>
                                    <div class="mt-3 flex gap-2">
                                        <form method="POST" action="{{ route('invitations.accept', $inv->token) }}" class="flex-1">
                                            @csrf
                                            <button class="w-full py-2 bg-emerald-600 text-white text-sm font-bold rounded-xl hover:bg-emerald-700">
                                                Accepter
                                            </button>
                                        </form>
                                        <form method="POST" action="{{ route('invitations.refuse', $inv->token) }}" class="flex-1">
                                            @csrf
                                            <button class="w-full py-2 bg-white text-zinc-600 text-sm font-bold rounded-xl border border-zinc-200 hover:bg-rose-50 hover:text-rose-600 hover:border-rose-100">
                                                Refuser
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Membres colocation (card dark) --}}
                <div class="bg-zinc-900 rounded-3xl p-6 text-white border border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold">Membres de la coloc</h3>
                        <span class="text-[10px] font-black px-2 py-1 rounded-full bg-white/10 uppercase tracking-widest">
                            {{ $activeColocation ? 'ACTIFS' : 'VIDE' }}
                        </span>
                    </div>

                    @if(!$activeColocation)
                        <div class="text-white/70 text-sm">Aucune colocation active.</div>
                    @else
                        <div class="space-y-3">
                            @foreach($activeColocation->members as $m)
                                <div class="rounded-2xl bg-white/5 border border-white/10 px-4 py-3 flex items-center justify-between">
                                    <div class="font-bold">{{ $m->name }}</div>
                                    <div class="text-xs text-white/70 font-semibold">
                                        {{ strtoupper($m->pivot->role) }} • {{ $m->reputation ?? 0 }} pts
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>
        </div>
    </div>
</x-app-layout>