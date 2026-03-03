<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'EasyColoc') }}</title>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased h-full bg-zinc-50">
        <div class="flex h-full overflow-hidden">
            <!-- Sidebar -->
            @include('layouts.navigation')

            <!-- Main Content Area -->
            <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
                <!-- Page Heading (Optional Top Bar) -->
                @isset($header)
                    <header class="bg-white/80 backdrop-blur-md border-b border-zinc-200 py-4 px-8 sticky top-0 z-10 flex items-center justify-between">
                        <div class="flex-1 min-w-0">
                            {{ $header }}
                        </div>
                        <div class="flex items-center gap-4">
                            <!-- User Status or Search could go here -->
                        </div>
                    </header>
                @endisset

                <!-- Page Content -->
                <main class="flex-1 overflow-y-auto focus:outline-none scroll-smooth">
                    <div class="py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
