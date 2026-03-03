<x-app-layout>
    <x-slot name="header">
        <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Mon Profil</h2>
    </x-slot>

    <div class="space-y-8 pb-12">
        <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-8 max-w-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="bg-white rounded-3xl border border-zinc-200 shadow-sm p-8 max-w-2xl">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="bg-rose-50 rounded-3xl border border-rose-100 shadow-sm p-8 max-w-2xl">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
