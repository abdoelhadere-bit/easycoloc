<x-app-layout>
    <div class="max-w-lg mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Invitation</h1>

        <p>Colocation : <b>{{ $invitation->colocation->name }}</b></p>
        <p>Email invité : <b>{{ $invitation->email }}</b></p>
        <p>Status : <b>{{ $invitation->status }}</b></p>

        @if($invitation->status === 'pending')
            <form method="POST" action="{{ route('invitations.accept', $invitation->token) }}" class="mt-4">
                @csrf
                <button class="px-4 py-2 bg-green-600 text-white rounded">Accepter</button>
            </form>

            <form method="POST" action="{{ route('invitations.refuse', $invitation->token) }}" class="mt-2">
                @csrf
                <button class="px-4 py-2 bg-red-600 text-white rounded">Refuser</button>
            </form>
        @endif
    </div>
</x-app-layout>