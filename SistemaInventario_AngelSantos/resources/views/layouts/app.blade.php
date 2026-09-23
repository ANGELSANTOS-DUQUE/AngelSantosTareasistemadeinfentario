<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Sistema de Control de Inventario') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-slate-900 text-slate-100 min-h-screen">
        <div class="min-h-screen flex flex-col bg-slate-950/60">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-slate-900/80 backdrop-blur border-b border-slate-800/80 shadow-sm">
                    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Flash Alerts -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-4 w-full">
                @if (session('success'))
                    <div class="flex items-center p-4 mb-4 text-emerald-300 bg-emerald-950/80 border border-emerald-800/60 rounded-xl shadow-lg backdrop-blur" role="alert">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 text-emerald-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm font-medium">{{ session('success') }}</div>
                    </div>
                @endif

                @if (session('error'))
                    <div class="flex items-center p-4 mb-4 text-rose-300 bg-rose-950/80 border border-rose-800/60 rounded-xl shadow-lg backdrop-blur" role="alert">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 text-rose-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm font-medium">{{ session('error') }}</div>
                    </div>
                @endif

                @if (session('warning'))
                    <div class="flex items-center p-4 mb-4 text-amber-300 bg-amber-950/80 border border-amber-800/60 rounded-xl shadow-lg backdrop-blur" role="alert">
                        <svg class="flex-shrink-0 w-5 h-5 mr-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                        </svg>
                        <div class="text-sm font-medium">{{ session('warning') }}</div>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="p-4 mb-4 text-rose-300 bg-rose-950/80 border border-rose-800/60 rounded-xl shadow-lg backdrop-blur">
                        <div class="font-medium text-sm mb-1">Por favor corrige los siguientes errores:</div>
                        <ul class="list-disc list-inside text-xs space-y-1 text-rose-300/90">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            </div>

            <!-- Page Content -->
            <main class="flex-1 py-6">
                {{ $slot }}
            </main>

            <footer class="border-t border-slate-800/60 py-4 text-center text-xs text-slate-500">
                &copy; {{ date('Y') }} Sistema de Control de Inventario para Mipymes. Todos los derechos reservados.
            </footer>
        </div>
    </body>
</html>
