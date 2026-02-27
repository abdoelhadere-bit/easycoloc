<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
    </x-slot>

    <div class="py-10 max-w-6xl mx-auto space-y-6">

        @if(session('success'))
            <div class="p-3 bg-green-100 border border-green-300 text-green-800 rounded">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="p-3 bg-red-100 border border-red-300 text-red-800 rounded">
                {{ $errors->first() }}
            </div>
        @endif

        <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
            <div class="p-4 bg-white rounded shadow">Users: <b>{{ $stats['users'] }}</b></div>
            <div class="p-4 bg-white rounded shadow">Colocs: <b>{{ $stats['colocations'] }}</b></div>
            <div class="p-4 bg-white rounded shadow">Total dépenses: <b>{{ number_format($stats['expenses_total'], 2) }} DH</b></div>
            <div class="p-4 bg-white rounded shadow">Paiements: <b>{{ $stats['payments'] }}</b></div>
            <div class="p-4 bg-white rounded shadow">Bannis: <b>{{ $stats['banned'] }}</b></div>
        </div>

        <div class="bg-white rounded shadow overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 text-left">Nom</th>
                        <th class="p-3 text-left">Email</th>
                        <th class="p-3 text-left">Role</th>
                        <th class="p-3 text-left">Reputation</th>
                        <th class="p-3 text-left">Status</th>
                        <th class="p-3 text-left">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($users as $u)
                        <tr class="border-t">
                            <td class="p-3">{{ $u->name }}</td>
                            <td class="p-3">{{ $u->email }}</td>
                            <td class="p-3">{{ $u->role }}</td>
                            <td class="p-3">{{ $u->reputation }}</td>
                            <td class="p-3">
                                @if($u->is_banned)
                                    <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded">BANNED</span>
                                @else
                                    <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded">ACTIVE</span>
                                @endif
                            </td>
                            <td class="p-3">
                                @if(!$u->is_banned)
                                    <form method="POST" action="{{ route('admin.users.ban', $u) }}">
                                        @csrf
                                        <button class="px-3 py-1 bg-red-600 text-white rounded">Ban</button>
                                    </form>
                                @else
                                    <form method="POST" action="{{ route('admin.users.unban', $u) }}">
                                        @csrf
                                        <button class="px-3 py-1 bg-gray-700 text-white rounded">Unban</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="p-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>
</x-app-layout>