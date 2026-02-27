<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>
    </x-slot>

    <div class="py-10 max-w-5xl mx-auto space-y-6">

        @if($errors->has('banned'))
            <div class="p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                {{ $errors->first('banned') }}
            </div>
        @endif

        <div class="bg-white rounded shadow p-6">
            <h3 class="font-bold text-lg mb-2">Ma colocation</h3>

            @if($activeColocation)
                <p class="mb-3">Colocation active : <b>{{ $activeColocation->name }}</b></p>
                <a class="px-4 py-2 bg-blue-600 text-white rounded"
                   href="{{ route('colocations.show', $activeColocation) }}">
                    Ouvrir
                </a>
            @else
                <p class="mb-3 text-gray-600">Aucune colocation active.</p>
                <a class="px-4 py-2 bg-green-600 text-white rounded"
                   href="{{ route('colocations.create') }}">
                    Créer une colocation
                </a>
            @endif
        </div>

        <div class="bg-white rounded shadow p-6">
            <h3 class="font-bold text-lg mb-2">Invitations reçues</h3>

            @if($pendingInvitations->isEmpty())
                <p class="text-gray-600">Aucune invitation en attente.</p>
            @else
                <ul class="space-y-2">
                    @foreach($pendingInvitations as $inv)
                        <li class="p-3 border rounded flex items-center justify-between">
                            <div>
                                <div>Colocation : <b>{{ $inv->colocation->name }}</b></div>
                                <div class="text-sm text-gray-600">Token: {{ $inv->token }}</div>
                            </div>
                            <div class="flex gap-2">
                                <form method="POST" action="{{ route('invitations.accept', $inv->token) }}">
                                    @csrf
                                    <button class="px-3 py-1 bg-green-600 text-white rounded">Accepter</button>
                                </form>
                                <form method="POST" action="{{ route('invitations.refuse', $inv->token) }}">
                                    @csrf
                                    <button class="px-3 py-1 bg-red-600 text-white rounded">Refuser</button>
                                </form>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>

        @if(auth()->user()->role === 'admin_global')
            <div class="bg-white rounded shadow p-6">
                <h3 class="font-bold text-lg mb-2">Admin</h3>
                <a class="px-4 py-2 bg-gray-800 text-white rounded" href="{{ route('admin.index') }}">
                    Ouvrir Admin Dashboard
                </a>
            </div>
        @endif

    </div>
</x-app-layout>