<aside x-data="{ mobileMenuOpen: false }" class="fixed inset-y-0 left-0 z-50 w-72 bg-white border-r border-zinc-200 flex flex-col transition-transform duration-300 lg:relative lg:translate-x-0" :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full'">
    <!-- Logo Section -->
    <div class="p-6 flex items-center gap-3">
        <div class="w-10 h-10 bg-brand-600 rounded-xl flex items-center justify-center text-white shadow-lg shadow-brand-200">
            <x-application-logo class="w-6 h-6 fill-current" />
        </div>
        <span class="text-xl font-bold tracking-tight text-zinc-900">EasyColoc</span>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-4 space-y-1 overflow-y-auto mt-4">
        <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider px-2 mb-2">Menu Principal</div>
        
        <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            <span>Tableau de bord</span>
        </x-nav-link>

        <x-nav-link href="{{route('colocations.index')}}" :active="request()->routeIs('colocations.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            <span>Mes Colocations</span>
        </x-nav-link>

        <x-nav-link :href="route('profile.edit')" :active="request()->routeIs('profile.*')">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
            <span>Mon Profil</span>
        </x-nav-link>

        @if(auth()->user()->role === 'admin_global')
            <div class="text-xs font-semibold text-zinc-400 uppercase tracking-wider px-2 mt-8 mb-2">Administration</div>
            <x-nav-link :href="route('admin.index')" :active="request()->routeIs('admin.*')">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                <span>Supervision</span>
            </x-nav-link>
        @endif
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-zinc-100 bg-zinc-50/50">
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open" class="flex items-center gap-3 w-full p-2 rounded-xl border border-transparent hover:bg-white hover:border-zinc-200 transition-all group">
                <div class="w-10 h-10 rounded-full bg-brand-100 flex items-center justify-center text-brand-700 font-bold border-2 border-white shadow-sm ring-1 ring-zinc-200">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 text-left">
                    <div class="text-sm font-semibold text-zinc-900 truncate">{{ Auth::user()->name }}</div>
                    <div class="text-xs text-zinc-500 truncate">{{ Auth::user()->email }}</div>
                </div>
                <svg class="w-4 h-4 text-zinc-400 group-hover:text-zinc-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
            </button>

            <!-- User Dropdown -->
            <div x-show="open" 
                 @click.away="open = false"
                 class="absolute bottom-full left-0 w-full mb-2 bg-white rounded-2xl shadow-xl border border-zinc-200 p-2 space-y-1">
                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 p-2 text-sm text-zinc-700 hover:bg-zinc-50 rounded-lg transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span>Mon Profil</span>
                </a>
                
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center gap-2 w-full p-2 text-sm text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Se déconnecter</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
