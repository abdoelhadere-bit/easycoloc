<x-app-layout>
    <div class="max-w-6xl mx-auto p-6 space-y-8 text-white">

        {{-- SUCCESS MESSAGE --}}
        @if(session('success'))
            <div class="p-3 bg-green-600 rounded text-white">
                {{ session('success') }}
            </div>
        @endif

        {{-- HEADER --}}
        <div>
            <h1 class="text-3xl font-bold">
                {{ $colocation->name }}
            </h1>
            <p class="text-gray-300 mt-1">
                Status : <span class="font-semibold">{{ $colocation->status }}</span>
            </p>
        </div>

        {{-- MEMBRES --}}
        <div>
            <h2 class="text-xl font-semibold mb-2">Membres actifs</h2>

            <div class="flex flex-wrap gap-3">
                @foreach($members as $m)
                    <div class="flex items-center gap-2 bg-slate-700 px-3 py-2 rounded">
                        <div>
                            {{ $m->name }}
                            <span class="text-gray-300 text-xs">({{ $m->pivot->role }})</span>
                        </div>

                        @if($colocation->isOwner(auth()->id()) && $m->pivot->role !== 'owner')
                            <form method="POST" action="{{ route('colocations.members.remove', [$colocation, $m]) }}"
                                  onsubmit="return confirm('Retirer ce membre ?');">
                                @csrf
                                <button class="px-3 py-1 bg-red-600 text-white rounded text-xs">Retirer</button>
                            </form>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
        
        @if($errors->has('leave')) 
            <div class="p-3 bg-red-600 rounded mb-4"> 
                {{ $errors->first('leave') }} 
            </div> 
        @endif

        {{-- ACTIONS --}}
        <div class="flex gap-3">
            <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                @csrf
                <button class="px-4 py-2 bg-gray-700 rounded">
                    Quitter
                </button>
            </form>

            @if($colocation->isOwner(auth()->id()))
                <form method="POST" action="{{ route('colocations.cancel', $colocation) }}"
                      onsubmit="return confirm('Annuler la colocation ?');">
                    @csrf
                    <button class="px-4 py-2 bg-red-600 rounded">Annuler</button>
                </form>
            @endif
        </div>


        @if($errors->has('category_delete')) 
            <div class="p-3 bg-red-600 rounded mb-4"> 
                {{ $errors->first('category_delete') }} 
            </div> 
        @endif
        @if($colocation->isOwner(auth()->id()))
        <div class="mt-8">
            <h2 class="text-xl font-semibold mb-3">Gestion des catégories</h2>

            {{-- Ajouter catégorie --}}
            <form method="POST" 
                  action="{{ route('categories.store', $colocation) }}"
                  class="flex gap-3 text-black mb-4">
                @csrf
                <input type="text" 
                       name="name" 
                       placeholder="Nouvelle catégorie"
                       class="px-3 py-2 rounded"
                       required>
                <button class="px-4 py-2 bg-blue-600 text-white rounded">
                    Ajouter
                </button>
            </form>

            {{-- Liste catégories --}}
            @foreach($categories as $category)
                <div class="flex items-center gap-3 mb-2">

                    {{-- Modifier --}}
                    <form method="POST" 
                          action="{{ route('categories.update', [$colocation, $category]) }}"
                          class="flex gap-2 text-black">
                        @csrf
                        @method('PATCH')

                        <input type="text" 
                               name="name"
                               value="{{ $category->name }}"
                               class="px-2 py-1 rounded">

                        <button class="px-3 py-1 bg-yellow-600 text-white rounded">
                            Modifier
                        </button>
                    </form>

                    {{-- Supprimer --}}
                    <form method="POST" 
                          action="{{ route('categories.destroy', [$colocation, $category]) }}">
                        @csrf
                        @method('DELETE')

                        <button class="px-3 py-1 bg-red-600 text-white rounded">
                            Supprimer
                        </button>
                    </form>
                </div>
            @endforeach
        </div>
        @endif
        {{-- ADD EXPENSE --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Ajouter une dépense</h2>

            <form method="POST"
                  action="{{ route('expenses.store', $colocation) }}"
                  class="flex flex-wrap gap-3 text-black">
                @csrf

                <input name="title"
                       placeholder="Titre"
                       class="px-3 py-2 rounded"
                       required>

                <input name="amount"
                       type="number"
                       step="0.01"
                       placeholder="Montant"
                       class="px-3 py-2 rounded"
                       required>

                <select name="category_id"
                        class="px-3 py-2 rounded">
                    <option value="">-- Catégorie --</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>

                <input name="date"
                       type="date"
                       class="px-3 py-2 rounded"
                       required>

                <button type="submit"
                        class="px-4 py-2 bg-blue-600 text-white rounded">
                    Ajouter
                </button>
            </form>
        </div>

        
        {{-- BALANCES --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Balances</h2>

            @foreach($balances as $b)
            <div class="flex justify-between py-1">
                <span>{{ $b['name'] }}</span>
                
                @if($b['balance'] > 0)
                <span class="text-green-400 font-semibold">
                    +{{ number_format($b['balance'], 2) }} DH
                </span>
                @elseif($b['balance'] < 0)
                <span class="text-red-400 font-semibold">
                            {{ number_format($b['balance'], 2) }} DH
                        </span>
                    @else
                        <span class="text-gray-400">0 DH</span>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- QUI DOIT A QUI --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Qui doit à qui</h2>

            @if(count($transactions) == 0)
                <p class="text-green-400">Aucune dette à régler ✅</p>
            @else
                @foreach($transactions as $t)
                    <div class="bg-slate-700 p-3 rounded-lg mb-2">
                        <p>
                            <span class="text-red-400 font-semibold">
                                {{ $t['from'] }}
                            </span>
                            doit payer
                            <span class="font-semibold">
                                {{ number_format($t['amount'], 2) }} DH
                            </span>
                            à
                            <span class="text-green-400 font-semibold">
                                {{ $t['to'] }}
                            </span>
                        </p>

                        @if(auth()->id() == $t['from_user_id'])
                            <form method="POST"
                                  action="{{ route('settlements.markPaid', $colocation) }}"
                                  class="mt-2">
                                @csrf
                                <input type="hidden"
                                       name="to_user_id"
                                       value="{{ $t['to_user_id'] }}">
                                <input type="hidden"
                                       name="amount"
                                       value="{{ $t['amount'] }}">
                                <button class="px-3 py-1 bg-green-600 rounded text-white">
                                    Marquer payé
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>

        {{-- STATISTIQUES --}}
        <div>
            <h2 class="text-xl font-semibold mb-3">Statistiques</h2>

            <p>Total dépenses : {{ number_format($total, 2) }} DH</p>
            <p>Part individuelle : {{ number_format($share, 2) }} DH</p>

            <h3 class="mt-4 font-bold">Dépenses par catégorie</h3>

            @foreach($categories as $category)
                <p>
                    {{ $category->name }} :
                    {{ number_format($statsByCategory[$category->id] ?? 0, 2) }} DH
                </p>
            @endforeach
        </div>

        {{-- INVITATION --}}
        @if($colocation->isOwner(auth()->id()))
        <div>
            <h2 class="text-xl font-semibold mb-3">
                Inviter un membre
            </h2>

            <form method="POST"
                  action="{{ route('invitations.store', $colocation) }}"
                  class="flex gap-3 text-black">
                  @csrf
                  
                  <input type="email"
                  name="email"
                  placeholder="Email du membre"
                  class="px-3 py-2 rounded"
                       required>

                <button type="submit"
                        class="px-4 py-2 bg-green-600 text-white rounded">
                    Inviter
                </button>
            </form>

            @if(session('invite_link'))
                <div class="mt-3">
                    <strong>Lien d’invitation :</strong><br>
                    <a href="{{ session('invite_link') }}"
                       class="text-blue-400 underline"
                       target="_blank">
                        {{ session('invite_link') }}
                    </a>
                </div>
            @endif
        </div>
        @endif
        
        {{-- DEPENSES --}}
        <div>
           <h2 class="text-xl font-semibold mb-3">Dépenses</h2>

            <form method="GET" class="mb-3 flex gap-2 items-center">
                <label class="text-sm text-gray-300">Mois :</label>
                <select name="month" class="text-black px-3 py-2 rounded" onchange="this.form.submit()">
                    <option value="">Tous</option>
                    @foreach($availableMonths as $m)
                        <option value="{{ $m }}" @selected($month === $m)>{{ $m }}</option>
                    @endforeach
                </select>
            </form>
    
            @if($expenses->count() == 0)
                <p>Aucune dépense enregistrée.</p>
            @else
                <table class="w-full text-sm text-left text-gray-300">
                    <thead>
                        <tr class="border-b border-slate-700">
                            <th class="pb-2">Date</th>
                            <th>Titre</th>
                            <th>Catégorie</th>
                            <th>Montant</th>
                            <th>Payé par</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($expenses as $expense)
                            <tr class="border-b border-slate-700">
                                <td class="py-2">{{ $expense->date }}</td>
                                <td>{{ $expense->title }}</td>
                                <td>{{ $expense->category?->name ?? '-' }}</td>
                                <td class="font-semibold">
                                    {{ number_format($expense->amount, 2) }} DH
                                </td>
                                <td>{{ $expense->payer->name }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</x-app-layout>