<x-app-layout>
    <div class="max-w-xl mx-auto p-6">
        <h1 class="text-2xl font-bold mb-4">Créer une colocation</h1>

        @if ($errors->any())
            <div class="bg-red-100 p-3 rounded mb-4">
                <ul class="list-disc ml-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('colocations.store') }}">
            @csrf
            <label class="block mb-2">Nom</label>
            <input name="name" class="border rounded w-full p-2" required value="{{ old('name') }}">

            <button class="mt-4 px-4 py-2 bg-black text-white rounded">
                Créer
            </button>
        </form>
    </div>
</x-app-layout>