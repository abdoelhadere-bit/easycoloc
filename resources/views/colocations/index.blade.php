<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between gap-4">
            <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Mes colocations</h2>

            <a href="{{ route('colocations.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-brand-600 text-white rounded-xl font-bold hover:bg-brand-700 transition-all active:scale-95">
                + Nouvelle colocation
            </a>
        </div>
    </x-slot>

    <div class="space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-2xl shadow-sm">
                {{ session('success') }}
            </div>
        @endif

        @if($colocations->isEmpty())
            <div class="bg-white rounded-3xl border border-zinc-200 p-10 text-center text-zinc-500">
                Aucune colocation pour le moment.
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($colocations as $c)
                    @php
                        $isOwner = $c->pivot->role === 'owner';
                        $hasLeft = !is_null($c->pivot->left_at);
                        $isCancelled = $c->status === 'cancelled';

                        // style "grisé" si quittée / cancelled
                        $cardOpacity = ($hasLeft || $isCancelled) ? 'opacity-60' : '';
                    @endphp

                    <div class="bg-white rounded-3xl border border-zinc-200 p-6 shadow-sm {{ $cardOpacity }}">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-12 h-12 rounded-2xl bg-brand-50 text-brand-700 flex items-center justify-center font-black">
                                    {{ strtoupper(substr($c->name, 0, 1)) }}
                                </div>

                                <div>
                                    <div class="font-black text-lg text-zinc-900">{{ $c->name }}</div>
                                    <div class="text-xs text-zinc-500 font-semibold">
                                        {{ $c->active_members_count }} membres
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                @if($isOwner)
                                    <span class="px-2.5 py-1 bg-amber-50 text-amber-700 text-xs font-black rounded-full border border-amber-100">
                                        OWNER
                                    </span>
                                @endif

                                @if($hasLeft)
                                    <span class="px-2.5 py-1 bg-zinc-100 text-zinc-600 text-xs font-black rounded-full">
                                        QUITTÉE
                                    </span>
                                @endif

                                <span class="px-2.5 py-1 bg-zinc-100 text-zinc-600 text-xs font-black rounded-full">
                                    {{ strtoupper($c->status) }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-2 gap-4 text-sm">
                            <div class="bg-zinc-50 rounded-2xl p-4 border border-zinc-100">
                                <div class="text-xs text-zinc-400 font-black uppercase tracking-widest">Dépenses</div>
                                <div class="text-2xl font-black text-zinc-900 mt-1">{{ $c->expenses_count }}</div>
                            </div>

                            <div class="bg-zinc-50 rounded-2xl p-4 border border-zinc-100">
                                <div class="text-xs text-zinc-400 font-black uppercase tracking-widest">Rôle</div>
                                <div class="text-2xl font-black text-zinc-900 mt-1">
                                    {{ $isOwner ? 'Owner' : 'Member' }}
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex items-center justify-end">
                            @can('view', $c)
                                <a href="{{ route('colocations.show', $c) }}"
                                   class="w-12 h-12 rounded-2xl bg-brand-600 text-white inline-flex items-center justify-center hover:bg-brand-700 transition-all active:scale-95">
                                    →
                                </a>
                            @else
                                <div class="w-12 h-12 rounded-2xl bg-zinc-200 text-zinc-500 inline-flex items-center justify-center cursor-not-allowed">
                                    ⦸
                                </div>
                            @endcan
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-app-layout>