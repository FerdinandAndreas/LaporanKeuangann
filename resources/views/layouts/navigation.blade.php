<nav x-data="{ open: false }" class="bg-slate-900 border-b border-slate-700 shadow-lg">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex items-center">
                <!-- Logo / Brand -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                        <span class="text-2xl">🌾</span>
                        <span class="text-white font-bold text-lg tracking-tight hidden sm:block">{{ config('app.name', 'Toko Beras') }}</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-1 sm:flex sm:ms-8">
                    <a href="{{ route('dashboard') }}"
                       class="px-3 py-2 rounded-md text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📊 Dashboard
                    </a>
                    <a href="{{ route('capitals.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('capitals.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        💰 Modal
                    </a>
                    <a href="{{ route('products.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('products.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        📦 Produk
                    </a>
                    <a href="{{ route('purchases.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('purchases.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        🛒 Pembelian
                    </a>
                    <a href="{{ route('sales.index') }}"
                       class="px-3 py-2 rounded-md text-sm font-semibold transition-colors duration-150 {{ request()->routeIs('sales.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                        🧾 Penjualan
                    </a>
                </div>
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center gap-2 px-3 py-2 rounded-md text-sm font-semibold text-slate-300 bg-slate-800 hover:bg-slate-700 hover:text-white border border-slate-600 focus:outline-none transition ease-in-out duration-150">
                            <span class="text-base">👤</span>
                            <span>{{ Auth::user()->name }}</span>
                            <svg class="fill-current h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                            </svg>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-slate-400 hover:text-white hover:bg-slate-700 focus:outline-none focus:bg-slate-700 focus:text-white transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-slate-800 border-t border-slate-700">
        <div class="pt-2 pb-3 space-y-1 px-3">
            <a href="{{ route('dashboard') }}"
               class="block px-3 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('dashboard') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                📊 Dashboard
            </a>
            <a href="{{ route('capitals.index') }}"
               class="block px-3 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('capitals.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                💰 Modal
            </a>
            <a href="{{ route('products.index') }}"
               class="block px-3 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('products.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                📦 Produk
            </a>
            <a href="{{ route('purchases.index') }}"
               class="block px-3 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('purchases.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                🛒 Pembelian
            </a>
            <a href="{{ route('sales.index') }}"
               class="block px-3 py-2 rounded-md text-sm font-semibold transition-colors {{ request()->routeIs('sales.*') ? 'bg-amber-500 text-slate-900' : 'text-slate-300 hover:bg-slate-700 hover:text-white' }}">
                🧾 Penjualan
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-3 pb-3 border-t border-slate-600 px-3">
            <div class="px-3 mb-2">
                <div class="font-semibold text-sm text-white">{{ Auth::user()->name }}</div>
                <div class="text-xs text-slate-400">{{ Auth::user()->email }}</div>
            </div>

            <div class="space-y-1">
                <a href="{{ route('profile.edit') }}"
                   class="block px-3 py-2 rounded-md text-sm text-slate-300 hover:bg-slate-700 hover:text-white font-medium transition-colors">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            class="w-full text-left px-3 py-2 rounded-md text-sm text-rose-400 hover:bg-rose-900 hover:text-rose-200 font-medium transition-colors">
                        Log Out
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>
