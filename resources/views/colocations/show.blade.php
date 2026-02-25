<x-app-layout>
    <div class="max-w-4xl mx-auto p-6 text-white">

        {{-- Titre --}}
        <h1 class="text-3xl font-bold mb-2">
            {{ $colocation->name }}
        </h1>

        <p class="mb-6">
            Status : <b>{{ $colocation->status }}</b>
        </p>

        {{-- Membres --}}
        <h2 class="text-xl font-semibold mb-2">Membres actifs</h2>
        <ul class="list-disc ml-6 mb-6">
            @foreach($members as $m)
                <li>
                    {{ $m->name }} ({{ $m->pivot->role }})
                </li>
            @endforeach
        </ul>

        {{-- Messages erreur quitter --}}

        @if($errors->has('leave'))
            <div class="p-3 bg-red-600 rounded mb-4">
                {{ $errors->first('leave') }}
            </div>
        @endif

        {{-- Actions colocation --}}
        <div class="flex gap-3 mb-8">
            <form method="POST" action="{{ route('colocations.leave', $colocation) }}">
                @csrf
                <button class="px-4 py-2 bg-gray-700 rounded">
                    Quitter
                </button>
            </form>

            <form method="POST" action="{{ route('colocations.cancel', $colocation) }}">
                @csrf
                <button class="px-4 py-2 bg-red-600 rounded">
                    Annuler
                </button>
            </form>
        </div>

        {{-- Ajouter dépense --}}
        <h2 class="text-xl font-semibold mb-3">Ajouter une dépense</h2>

        <form method="POST" action="{{ route('expenses.store', $colocation) }}"
              class="flex gap-3 mb-8 text-black">
            @csrf

            <input name="title" placeholder="Titre"
                   class="px-2 py-1 rounded" required>

            <input name="amount" type="number" step="0.01"
                   placeholder="Montant"
                   class="px-2 py-1 rounded" required>

            <input name="date" type="date"
                   class="px-2 py-1 rounded" required>

            <button type="submit"
                    class="px-4 py-1 bg-blue-600 text-white rounded">
                Ajouter
            </button>
        </form>

        
        {{-- Balances --}}
        <h2 class="text-xl font-semibold mb-3">Balances</h2>
        
        @foreach($balances as $b)
            <p>
                {{ $b['name'] }} :
                {{ number_format($b['balance'], 2) }} DH
            </p>
        @endforeach

        {{-- Qui doit à qui --}}
        <h2 class="text-xl font-semibold mt-6 mb-3">
           @foreach($transactions as $t)
                <div class="mb-2">
                    <p>
                        {{ $t['from'] }} doit payer 
                        {{ number_format($t['amount'], 2) }} DH 
                        à {{ $t['to'] }}
                    </p>
            
                    @if(auth()->id() == $t['from_user_id'])
                        <form method="POST"
                              action="{{ route('settlements.markPaid', $colocation) }}">
                            @csrf
                            <input type="hidden" name="to_user_id" value="{{ $t['to_user_id'] }}">
                            <input type="hidden" name="amount" value="{{ $t['amount'] }}">
                            <button class="px-3 py-1 bg-green-600 rounded text-white">
                                Marquer payé
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        {{-- Liste des dépenses --}}
        <h2 class="text-xl font-semibold mb-3">Dépenses</h2>

        @if($expenses->count() == 0)
            <p class="mb-6">Aucune dépense enregistrée.</p>
        @else
            <ul class="mb-8">
                @foreach($expenses as $expense)
                    <li class="mb-1">
                        {{ $expense->date }} —
                        {{ $expense->title }} —
                        {{ number_format($expense->amount, 2) }} DH —
                        Payé par {{ $expense->payer->name }}
                    </li>
                @endforeach
            </ul>
        @endif

        {{-- Invitation --}}
        <h2 class="text-xl font-semibold mt-8 mb-3">
            Inviter un membre
        </h2>

        <form method="POST"
              action="{{ route('invitations.store', $colocation) }}"
              class="flex gap-3 text-black">
            @csrf

            <input type="email"
                   name="email"
                   placeholder="Email du membre"
                   class="px-2 py-1 rounded"
                   required>

            <button type="submit"
                    class="px-4 py-1 bg-green-600 text-white rounded">
                Inviter
            </button>
        </form>

        @if(session('invite_link'))
            <div class="mt-4">
                <strong>Lien d’invitation :</strong><br>
                <a href="{{ session('invite_link') }}"
                   class="text-blue-400 underline"
                   target="_blank">
                    {{ session('invite_link') }}
                </a>
            </div>
        @endif

    </div>
</x-app-layout>
