<nav x-data="{ open: false, catalogoOpen: false, inventarioOpen: false }" class="bg-slate-900 border-b border-slate-800 sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <!-- Left Side: Brand + Main Links -->
            <div class="flex items-center space-x-4">
                <!-- Brand / Logo -->
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 text-emerald-400 font-bold text-lg tracking-wide hover:text-emerald-300 transition">
                    <div class="p-2 bg-emerald-500/10 border border-emerald-500/30 rounded-lg">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <span class="hidden md:inline font-semibold">Inventario<span class="text-white">Pro</span></span>
                </a>

                <!-- Desktop Navigation Links -->
                <div class="hidden md:flex md:space-x-2 md:items-center">
                    <a href="{{ route('dashboard') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('dashboard') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Dashboard
                    </a>

                    @if (Auth::user()->isSuperAdmin() || Auth::user()->hasPermissionTo('empresas.view'))
                        <a href="{{ route('empresas.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('empresas.*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            Empresas
                        </a>
                    @endif

                    <a href="{{ route('sucursales.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('sucursales.*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Sucursales
                    </a>

                    <a href="{{ route('areas.index') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('areas.*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Áreas
                    </a>

                    @if(Route::has('items.index'))
                    <!-- Dropdown Catálogo -->
                    <div class="relative" x-data="{ openCat: false }" @click.outside="openCat = false">
                        <button @click="openCat = !openCat" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs(['items.*', 'categorias.*', 'unidades.*', 'proveedores.*']) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>Catálogo</span>
                            <svg class="w-4 h-4 ml-1.5 transition-transform" :class="{'rotate-180': openCat}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openCat" x-transition class="absolute left-0 mt-2 w-48 bg-slate-800 border border-slate-700 rounded-xl shadow-xl py-2 z-50">
                            <a href="{{ route('items.index') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Ítems (Productos)</a>
                            <a href="{{ route('categorias.index') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Categorías</a>
                            <a href="{{ route('unidades.index') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Unidades de Medida</a>
                            <a href="{{ route('proveedores.index') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Proveedores</a>
                        </div>
                    </div>
                    @endif

                    @if(Route::has('movimientos.index'))
                    <!-- Dropdown Inventario -->
                    <div class="relative" x-data="{ openInv: false }" @click.outside="openInv = false">
                        <button @click="openInv = !openInv" class="flex items-center px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs(['movimientos.*', 'inventario.*']) ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                            <span>Inventario</span>
                            <svg class="w-4 h-4 ml-1.5 transition-transform" :class="{'rotate-180': openInv}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                        <div x-show="openInv" x-transition class="absolute left-0 mt-2 w-52 bg-slate-800 border border-slate-700 rounded-xl shadow-xl py-2 z-50">
                            <a href="{{ route('inventario.stock') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Stock por Área</a>
                            <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Registrar Entrada</a>
                            <a href="{{ route('movimientos.create', ['tipo' => 'salida']) }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Registrar Salida</a>
                            <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Registrar Traslado</a>
                            <a href="{{ route('movimientos.create', ['tipo' => 'ajuste']) }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Registrar Ajuste</a>
                            <div class="border-t border-slate-700 my-1"></div>
                            <a href="{{ route('movimientos.index') }}" class="block px-4 py-2 text-sm text-slate-200 hover:bg-slate-700 hover:text-emerald-300">Historial de Movimientos</a>
                        </div>
                    </div>
                    @endif

                    @if(Route::has('reportes.inventario'))
                    <a href="{{ route('reportes.inventario') }}" class="px-3 py-2 rounded-lg text-sm font-medium transition {{ request()->routeIs('reportes.*') ? 'bg-emerald-500/20 text-emerald-300 border border-emerald-500/40' : 'text-slate-300 hover:bg-slate-800 hover:text-white' }}">
                        Reportes
                    </a>
                    @endif
                </div>
            </div>

            <!-- Right Side: User Details & Profile Dropdown -->
            <div class="hidden md:flex md:items-center md:space-x-4">
                <!-- User Empresa / Role Badge -->
                <div class="flex flex-col items-end text-xs">
                    <span class="font-semibold text-slate-200">
                        {{ Auth::user()->empresa->nombre ?? (Auth::user()->isSuperAdmin() ? 'Administración Global' : 'Sin Empresa') }}
                    </span>
                    <span class="text-emerald-400 capitalize">
                        {{ str_replace('_', ' ', Auth::user()->roles->first()->name ?? 'Usuario') }}
                    </span>
                </div>

                <!-- Profile Dropdown -->
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="flex items-center space-x-2 p-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 border border-slate-700 transition focus:outline-none">
                            <div class="w-8 h-8 rounded-lg bg-emerald-600 flex items-center justify-center font-bold text-white text-sm">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <span class="text-sm font-medium text-slate-200">{{ Auth::user()->name }}</span>
                            <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')" class="text-slate-200 hover:bg-slate-700">
                            {{ __('Mi Perfil') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();" class="text-rose-400 hover:bg-slate-700">
                                {{ __('Cerrar Sesión') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Mobile Hamburger Button -->
            <div class="flex items-center md:hidden">
                <button @click="open = ! open" class="p-2 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800 focus:outline-none">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden md:hidden bg-slate-900 border-b border-slate-800 px-4 pt-2 pb-4 space-y-1">
        <a href="{{ route('dashboard') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('dashboard') ? 'bg-emerald-500/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Dashboard
        </a>
        @if (Auth::user()->isSuperAdmin() || Auth::user()->hasPermissionTo('empresas.view'))
            <a href="{{ route('empresas.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('empresas.*') ? 'bg-emerald-500/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-800' }}">
                Empresas
            </a>
        @endif
        <a href="{{ route('sucursales.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('sucursales.*') ? 'bg-emerald-500/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Sucursales
        </a>
        <a href="{{ route('areas.index') }}" class="block px-3 py-2 rounded-lg text-sm font-medium {{ request()->routeIs('areas.*') ? 'bg-emerald-500/20 text-emerald-300' : 'text-slate-300 hover:bg-slate-800' }}">
            Áreas
        </a>

        @if(Route::has('items.index'))
            <div class="pt-2 border-t border-slate-800">
                <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Catálogo</span>
                <a href="{{ route('items.index') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Ítems</a>
                <a href="{{ route('categorias.index') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Categorías</a>
                <a href="{{ route('unidades.index') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Unidades</a>
                <a href="{{ route('proveedores.index') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Proveedores</a>
            </div>
        @endif

        @if(Route::has('movimientos.index'))
            <div class="pt-2 border-t border-slate-800">
                <span class="px-3 text-xs font-semibold text-slate-500 uppercase tracking-wider">Inventario</span>
                <a href="{{ route('inventario.stock') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Stock por Área</a>
                <a href="{{ route('movimientos.create', ['tipo' => 'entrada']) }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Entrada</a>
                <a href="{{ route('movimientos.create', ['tipo' => 'salida']) }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Salida</a>
                <a href="{{ route('movimientos.create', ['tipo' => 'traslado']) }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Traslado</a>
                <a href="{{ route('movimientos.create', ['tipo' => 'ajuste']) }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Ajuste</a>
                <a href="{{ route('movimientos.index') }}" class="block px-3 py-1.5 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Historial</a>
            </div>
        @endif

        <div class="pt-3 border-t border-slate-800">
            <div class="px-3 py-1 text-xs text-slate-400">
                Logueado como: <strong class="text-white">{{ Auth::user()->name }}</strong>
            </div>
            <a href="{{ route('profile.edit') }}" class="block px-3 py-2 rounded-lg text-sm text-slate-300 hover:bg-slate-800">Mi Perfil</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-lg text-sm text-rose-400 hover:bg-slate-800">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </div>
</nav>
