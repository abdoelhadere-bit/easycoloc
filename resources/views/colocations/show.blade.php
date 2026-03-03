<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-black text-zinc-900 tracking-tight flex items-center gap-3">
                    {{ $colocation->name }}
                    <span class="px-2.5 py-1 bg-brand-50 text-brand-700 text-xs font-bold rounded-full border border-brand-100 uppercase tracking-wider">
                        {{ $colocation->status }}
                    </span>
                </h2>
            </div>

            <div class="flex items-center gap-2">
                <a href="{{ route('dashboard') }}"
                   class="px-4 py-2 bg-white text-zinc-700 text-sm font-bold rounded-xl border border-zinc-200 hover:bg-zinc-50 transition-all active:scale-95">
                    Retour
                </a>

                {{-- Quitter : seulement si colocation active + user encore membre actif --}}
                @if(!$readOnly && $pivot->role !== 'owner')
                    <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                        @csrf
                        <button class="px-4 py-2 bg-white text-zinc-600 text-sm font-bold rounded-xl border border-zinc-200 hover:bg-zinc-50 transition-all active:scale-95">
                            Quitter
                        </button>
                    </form>
                @endif

                {{-- Annuler coloc : seulement owner + pas lecture seule + status active --}}
                @if(!$readOnly && $colocation->isOwner(auth()->id()) && $colocation->status === 'active')
                    <form method="POST" action="{{ route('colocations.cancel', $colocation) }}"
                          onsubmit="return confirm('Annuler la colocation ? Cette action est irréversible.');">
                        @csrf
                        <button class="px-4 py-2 bg-rose-600 text-white text-sm font-bold rounded-xl hover:bg-rose-700 transition-all active:scale-95">
                            Annuler la colocation
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </x-slot>

    <div x-data="{ expenseOpen:false, inviteOpen:false, categoriesOpen:false }" class="space-y-6 pb-12">

        {{-- Success --}}
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl flex items-center gap-3 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span class="font-medium text-sm">{{ session('success') }}</span>
            </div>
        @endif

        {{-- Errors --}}
        @if($errors->any())
            <div class="p-4 bg-rose-50 border border-rose-100 text-rose-700 rounded-2xl shadow-sm">
                <ul class="list-disc pl-5 text-sm font-medium space-y-1">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- ✅ Bandeau lecture seule --}}
        @if($readOnly)
            <div class="p-4 bg-white border border-zinc-200 text-zinc-700 rounded-2xl flex items-start gap-3 shadow-sm">
                <div class="mt-0.5">
                    <svg class="w-5 h-5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">
                    @if(!is_null($pivot->left_at))
                        Vous avez quitté cette colocation. Vous pouvez consulter l'historique mais vous ne pouvez plus ajouter de dépenses ou actions.
                    @else
                        Cette colocation est annulée. Consultation en lecture seule.
                    @endif
                </div>
            </div>
        @endif

        {{-- Alerte si pas de catégories --}}
        @if(!$readOnly && $categories->count() === 0)
            <div class="p-4 bg-amber-50 border border-amber-100 text-amber-700 rounded-2xl flex items-start gap-3 shadow-sm mb-6">
                <div class="mt-0.5">
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
                <div class="text-sm font-medium">
                    {{-- Si owner, lien vers modal catégories --}}
                    @if($colocation->isOwner(auth()->id()))
                        Attention : vous devez <button @click="categoriesOpen=true" class="font-black underline decoration-2 underline-offset-2 hover:text-amber-900 transition-colors">créer au moins une catégorie</button> avant de pouvoir ajouter des dépenses.
                    @else
                        Attention : cette colocation n'a pas encore de catégories. L'administrateur doit en créer une avant que vous ne puissiez ajouter de dépenses.
                    @endif
                </div>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- LEFT: Expenses --}}
            <div class="lg:col-span-2">
                <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm overflow-hidden">
                    <div class="p-6 border-b border-zinc-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
                            </svg>
                            <h3 class="font-bold text-zinc-900">Dépenses récentes</h3>
                        </div>

                        <div class="flex items-center gap-3">
                            <form method="GET" class="flex items-center gap-2">
                                <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">Filtrer par mois</span>
                                <select name="month"
                                        class="text-xs font-bold text-zinc-600 bg-zinc-50 border-zinc-200 rounded-xl px-3 py-2 focus:ring-brand-500 transition-all cursor-pointer"
                                        onchange="this.form.submit()">
                                    <option value="">Tous les mois</option>
                                    @foreach($availableMonths as $m)
                                        <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
                                    @endforeach
                                </select>
                            </form>

                            {{-- Petit résumé : nb + total (si fourni par controller) --}}
                            @isset($expensesCount, $expensesTotal)
                                <div class="text-xs font-bold text-zinc-400">
                                    {{ $expensesCount }} dépense(s)
                                    <span class="mx-2">|</span>
                                    Total: <span class="text-brand-600">{{ number_format($expensesTotal, 2) }} DH</span>
                                </div>
                            @endisset

                            {{-- Catégories : seulement si owner + pas lecture seule --}}
                            @if(!$readOnly && $colocation->isOwner(auth()->id()))
                                <button type="button"
                                        @click="categoriesOpen=true"
                                        class="px-3 py-2 bg-white text-zinc-700 text-xs font-black rounded-xl border border-zinc-200 hover:bg-zinc-50 transition-all active:scale-95">
                                    Catégories
                                </button>
                            @endif

                            {{-- Nouvelle dépense : désactivée en lecture seule ou si pas de catégories --}}
                            @if(!$readOnly)
                                @if($categories->count() > 0)
                                    <button type="button"
                                            @click="expenseOpen=true"
                                            class="px-4 py-2 bg-brand-600 text-white text-sm font-black rounded-xl hover:bg-brand-700 transition-all active:scale-95">
                                        + Nouvelle dépense
                                    </button>
                                @else
                                    <button type="button"
                                            disabled
                                            class="px-4 py-2 bg-zinc-100 text-zinc-400 text-sm font-black rounded-xl border border-zinc-200 cursor-not-allowed"
                                            title="Créez d'abord une catégorie">
                                        + Nouvelle dépense
                                    </button>
                                @endif
                            @endif
                        </div>
                    </div>

                    @if($expenses->isEmpty())
                        <div class="py-16 text-center text-zinc-500 font-medium">
                            Aucune dépense.
                        </div>
                    @else
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead>
                                    <tr class="bg-zinc-50 text-left border-b border-zinc-100">
                                        <th class="px-6 py-4 text-xs font-black text-zinc-400 uppercase tracking-widest">Titre / Catégorie</th>
                                        <th class="px-6 py-4 text-xs font-black text-zinc-400 uppercase tracking-widest">Payeur</th>
                                        <th class="px-6 py-4 text-xs font-black text-zinc-400 uppercase tracking-widest text-right">Montant</th>
                                        <th class="px-6 py-4 text-xs font-black text-zinc-400 uppercase tracking-widest text-right">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($expenses as $expense)
                                        <tr class="hover:bg-zinc-50/50 transition-colors border-b border-zinc-50 last:border-0">
                                            <td class="px-6 py-4">
                                                <div class="font-extrabold text-zinc-900">{{ $expense->title }}</div>
                                                <div class="text-xs text-zinc-400 font-semibold">
                                                    {{ $expense->category?->name ?? 'Sans catégorie' }}
                                                    <span class="mx-2">•</span>
                                                    {{ $expense->date }}
                                                </div>
                                            </td>

                                            <td class="px-6 py-4">
                                                <div class="flex items-center gap-2">
                                                    <div class="w-8 h-8 rounded-full bg-brand-50 flex items-center justify-center text-brand-700 text-xs font-black">
                                                        {{ strtoupper(substr($expense->payer->name, 0, 1)) }}
                                                    </div>
                                                    <span class="text-sm font-bold text-zinc-700">{{ $expense->payer->name }}</span>
                                                </div>
                                            </td>

                                            <td class="px-6 py-4 text-right">
                                                <span class="text-base font-black text-zinc-900">{{ number_format($expense->amount, 2) }} DH</span>
                                            </td>

                                            <td class="px-6 py-4 text-right">
                                                {{-- Suppression : seulement owner + pas lecture seule --}}
                                                @if(!$readOnly && $colocation->isOwner(auth()->id()))
                                                    <form method="POST" action="{{ route('expenses.destroy', $expense) }}" onsubmit="return confirm('Supprimer cette dépense ?');" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button class="w-9 h-9 inline-flex items-center justify-center rounded-xl border border-zinc-200 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-700 transition-all">
                                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                @else
                                                    <span class="text-zinc-300 text-sm font-bold">—</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>

            {{-- RIGHT: Who owes + Members --}}
            <div class="space-y-6">

                {{-- Qui doit à qui --}}
                <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-6">
                    <h3 class="font-bold text-zinc-900 mb-4">Qui doit à qui ?</h3>

                    @if(count($transactions) === 0)
                        <div class="rounded-2xl bg-zinc-50 border border-zinc-100 p-6 text-center text-zinc-500 font-medium">
                            Aucun remboursement en attente.
                        </div>
                    @else
                        <div class="space-y-3">
                            @foreach($transactions as $t)
                                <div class="rounded-2xl border border-zinc-200 p-4">
                                    <div class="text-sm font-bold text-zinc-700">
                                        <span class="text-rose-600">{{ $t['from'] }}</span>
                                        <span class="mx-2 text-zinc-400">→</span>
                                        <span class="text-emerald-600">{{ $t['to'] }}</span>
                                    </div>

                                    <div class="mt-3 flex items-center justify-between">
                                        <div class="text-lg font-black text-zinc-900">
                                            {{ number_format($t['amount'], 2) }} DH
                                        </div>

                                        {{-- Marquer réglé : seulement si pas lecture seule + le débiteur connecté --}}
                                        @if(!$readOnly && auth()->id() == $t['from_user_id'])
                                            <form method="POST" action="{{ route('settlements.markPaid', $colocation) }}">
                                                @csrf
                                                <input type="hidden" name="to_user_id" value="{{ $t['to_user_id'] }}">
                                                <input type="hidden" name="amount" value="{{ $t['amount'] }}">
                                                <button class="px-4 py-2 bg-brand-600 text-white text-xs font-black rounded-xl hover:bg-brand-700 transition-all active:scale-95">
                                                    Marquer réglé
                                                </button>
                                            </form>
                                        @else
                                            <span class="text-xs font-bold text-zinc-400 uppercase tracking-widest">—</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Membres (dark card) --}}
                <div class="bg-zinc-900 rounded-3xl p-6 text-white border border-white/10">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold">Membres de la coloc</h3>
                        <span class="text-[10px] font-black px-2 py-1 rounded-full bg-white/10 uppercase tracking-widest">
                            {{ $readOnly ? 'ARCHIVÉE' : 'ACTIFS' }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        @foreach($members as $m)
                            <div class="flex items-center justify-between rounded-2xl bg-white/5 border border-white/10 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center font-black">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>

                                    <div>
                                        <div class="font-bold">{{ $m->name }}</div>
                                        <div class="text-xs text-white/70 font-semibold">
                                            {{ strtoupper($m->pivot->role) }} • {{ $m->reputation }} pts
                                        </div>
                                    </div>
                                </div>

                                {{-- Retirer membre : seulement owner + pas lecture seule --}}
                                @if(!$readOnly && $colocation->isOwner(auth()->id()) && $m->pivot->role !== 'owner')
                                    <form method="POST" action="{{ route('colocations.members.remove', [$colocation, $m]) }}"
                                          onsubmit="return confirm('Retirer ce membre ?');">
                                        @csrf
                                        <button type="submit" class="w-10 h-10 rounded-xl bg-white/10 hover:bg-white/20 transition-all inline-flex items-center justify-center">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    {{-- Inviter : seulement owner + pas lecture seule --}}
                    @if(!$readOnly && $colocation->isOwner(auth()->id()))
                        <button type="button"
                                @click="inviteOpen=true"
                                class="mt-4 w-full py-3 rounded-2xl bg-white/10 hover:bg-white/20 transition-all font-black text-sm">
                            Inviter un membre
                        </button>
                    @endif
                </div>

                {{-- Réputation --}}
                <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="font-bold text-zinc-900">Réputation</h3>
                        <span class="text-xs font-black text-zinc-400 uppercase tracking-widest">+1 / -1</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($members as $m)
                            @php $rep = (int) ($m->reputation ?? 0); @endphp

                            <div class="flex items-center justify-between rounded-2xl border border-zinc-200 px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-2xl bg-zinc-100 flex items-center justify-center font-black text-zinc-700">
                                        {{ strtoupper(substr($m->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="font-bold text-zinc-900">{{ $m->name }}</div>
                                        <div class="text-xs text-zinc-500 font-semibold">
                                            {{ strtoupper($m->pivot->role) }}
                                        </div>
                                    </div>
                                </div>

                                <span class="px-3 py-1 rounded-full text-sm font-black
                                    {{ $rep >= 0 ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100' }}">
                                    {{ $rep >= 0 ? '+' : '' }}{{ $rep }} pts
                                </span>
                            </div>
                        @endforeach
                    </div>

                    <div class="mt-4 rounded-2xl bg-zinc-50 border border-zinc-100 p-4 text-sm text-zinc-700">
                        <div class="font-bold mb-1">Règles</div>
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Départ / annulation <b>sans dette</b> : <b>+1</b></li>
                            <li>Départ / annulation <b>avec dette</b> : <b>-1</b></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        {{-- MODALS : on les affiche seulement si pas lecture seule --}}
        @if(!$readOnly)

            {{-- MODAL: Nouvelle dépense --}}
            <div x-show="expenseOpen" x-cloak class="fixed inset-0 z-50">
                <div class="absolute inset-0 bg-black/40" @click="expenseOpen=false"></div>
                <div class="absolute inset-0 flex items-center justify-center p-4">
                    <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-zinc-200 overflow-hidden">
                        <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                            <h3 class="font-black text-zinc-900">Nouvelle dépense</h3>
                            <button type="button" @click="expenseOpen=false" class="w-9 h-9 rounded-xl border border-zinc-200 hover:bg-zinc-50 inline-flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>

                        <form method="POST" action="{{ route('expenses.store', $colocation) }}" class="p-5 space-y-4">
                            @csrf

                            <div>
                                <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Libellé</label>
                                <input name="title" required
                                       class="mt-1 w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-brand-500"
                                       placeholder="Ex: facture wifi">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Montant (DH)</label>
                                    <input name="amount" type="number" step="0.01" required
                                           class="mt-1 w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-bold focus:ring-brand-500"
                                           placeholder="0.00">
                                </div>
                                <div>
                                    <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Date</label>
                                    <input name="date" type="date" required value="{{ date('Y-m-d') }}"
                                           class="mt-1 w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-brand-500">
                                </div>
                            </div>

                            <div>
                                <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Catégorie</label>
                                <select name="category_id"
                                        class="mt-1 w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-brand-500">
                                    <option value="">-- Sélectionner --</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="flex items-center justify-end gap-2 pt-2">
                                <button type="button" @click="expenseOpen=false"
                                        class="px-4 py-2 rounded-xl border border-zinc-200 bg-white text-zinc-700 text-sm font-black hover:bg-zinc-50">
                                    Annuler
                                </button>
                                <button type="submit"
                                        class="px-5 py-2 rounded-xl bg-brand-600 text-white text-sm font-black hover:bg-brand-700">
                                    Enregistrer
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- MODAL: Inviter (owner seulement) --}}
            @if($colocation->isOwner(auth()->id()))
                <div x-show="inviteOpen" x-cloak class="fixed inset-0 z-50">
                    <div class="absolute inset-0 bg-black/40" @click="inviteOpen=false"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-zinc-200 overflow-hidden">
                            <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                                <h3 class="font-black text-zinc-900">Inviter un membre</h3>
                                <button type="button" @click="inviteOpen=false" class="w-9 h-9 rounded-xl border border-zinc-200 hover:bg-zinc-50 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <form method="POST" action="{{ route('invitations.store', $colocation) }}" class="p-5 space-y-4">
                                @csrf
                                <div>
                                    <label class="text-xs font-black text-zinc-400 uppercase tracking-widest">Email</label>
                                    <input type="email" name="email" required
                                           class="mt-1 w-full bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-semibold focus:ring-brand-500"
                                           placeholder="email@exemple.com">
                                </div>

                                <div class="flex items-center justify-end gap-2 pt-2">
                                    <button type="button" @click="inviteOpen=false"
                                            class="px-4 py-2 rounded-xl border border-zinc-200 bg-white text-zinc-700 text-sm font-black hover:bg-zinc-50">
                                        Annuler
                                    </button>
                                    <button type="submit"
                                            class="px-5 py-2 rounded-xl bg-zinc-900 text-white text-sm font-black hover:bg-zinc-800">
                                        Envoyer
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            {{-- MODAL: Catégories (owner seulement) --}}
            @if($colocation->isOwner(auth()->id()))
                <div x-show="categoriesOpen" x-cloak class="fixed inset-0 z-50">
                    <div class="absolute inset-0 bg-black/40" @click="categoriesOpen=false"></div>
                    <div class="absolute inset-0 flex items-center justify-center p-4">
                        <div class="w-full max-w-lg bg-white rounded-3xl shadow-xl border border-zinc-200 overflow-hidden">
                            <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                                <h3 class="font-black text-zinc-900">Catégories</h3>
                                <button type="button" @click="categoriesOpen=false" class="w-9 h-9 rounded-xl border border-zinc-200 hover:bg-zinc-50 inline-flex items-center justify-center">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="p-5 space-y-4">
                                <form method="POST" action="{{ route('categories.store', $colocation) }}" class="flex gap-2">
                                    @csrf
                                    <input type="text" name="name" required
                                           class="flex-1 bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-3 text-sm font-semibold"
                                           placeholder="Nom de catégorie...">
                                    <button class="px-4 py-3 rounded-2xl bg-brand-600 text-white text-sm font-black hover:bg-brand-700">
                                        Ajouter
                                    </button>
                                </form>

                                <div class="space-y-2 max-h-64 overflow-y-auto pr-2">
                                    @foreach($categories as $category)
                                        <div class="flex items-center gap-2">
                                            <form method="POST" action="{{ route('categories.update', [$colocation, $category]) }}" class="flex-1 flex gap-2">
                                                @csrf
                                                @method('PATCH')
                                                <input type="text" name="name" value="{{ $category->name }}"
                                                       class="flex-1 bg-zinc-50 border border-zinc-200 rounded-2xl px-4 py-2 text-sm font-semibold">
                                                <button class="px-3 py-2 rounded-2xl border border-zinc-200 bg-white text-zinc-800 text-xs font-black hover:bg-zinc-50">
                                                    OK
                                                </button>
                                            </form>

                                            <form method="POST" action="{{ route('categories.destroy', [$colocation, $category]) }}"
                                                  onsubmit="return confirm('Supprimer cette catégorie ?');">
                                                @csrf
                                                @method('DELETE')
                                                <button class="w-10 h-10 rounded-2xl border border-zinc-200 hover:bg-rose-50 hover:border-rose-100 hover:text-rose-700 transition-all inline-flex items-center justify-center">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="flex justify-end pt-2">
                                    <button type="button" @click="categoriesOpen=false"
                                            class="px-4 py-2 rounded-xl border border-zinc-200 bg-white text-zinc-700 text-sm font-black hover:bg-zinc-50">
                                        Fermer
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

        @endif {{-- end readOnly modals --}}

    </div>
</x-app-layout>