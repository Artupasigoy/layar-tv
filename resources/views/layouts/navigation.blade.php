<nav x-data="{ open: false }" class="bg-white border-b border-gray-100 shadow-sm z-30">

    <!-- Mobile Top Bar -->
    <div
        class="md:hidden flex items-center justify-between h-16 px-4 bg-white border-b border-gray-200 fixed w-full top-0 z-40">
        <div class="flex items-center gap-2">
            <x-application-logo class="block h-8 w-auto fill-current text-indigo-600" />
            <span class="font-bold text-lg text-gray-800">Web Layar TV</span>
        </div>
        <button @click="open = !open"
            class="p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
            <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                <path :class="{'hidden': open, 'inline-flex': !open }" class="inline-flex" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                <path :class="{'hidden': !open, 'inline-flex': open }" class="hidden" stroke-linecap="round"
                    stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    <!-- Sidebar (Desktop: Fixed, Mobile: Off-canvas) -->
    <div :class="{'translate-x-0': open, '-translate-x-full': !open}"
        class="fixed top-0 left-0 bottom-0 w-64 bg-white border-r border-gray-200 transform transition-transform duration-300 ease-in-out z-50 md:translate-x-0 md:fixed md:inset-y-0 h-screen flex flex-col shadow-xl md:shadow-none">

        <!-- Logo Area -->
        <div class="h-16 flex-shrink-0 flex items-center justify-between px-6 border-b border-gray-200 bg-gray-50">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <x-application-logo class="block h-9 w-auto fill-current text-indigo-600" />
                <span class="font-bold text-xl text-gray-800">Web Layar TV</span>
            </a>
            <!-- Mobile Close Button -->
            <button @click="open = false" class="md:hidden text-gray-400 hover:text-gray-500 focus:outline-none">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Navigation Links (Scrollable Area) -->
        <div class="flex-1 overflow-y-auto p-4 space-y-2">
            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</p>

            <a href="{{ route('dashboard') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                </svg>
                Dashboard
            </a>

            <a href="{{ route('admin.media') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.media') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.media') ? 'text-indigo-600' : 'text-gray-400' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                </svg>
                Media Manager
            </a>

            <a href="{{ route('admin.settings') }}"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg transition-colors {{ request()->routeIs('admin.settings') ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 {{ request()->routeIs('admin.settings') ? 'text-indigo-600' : 'text-gray-400' }}"
                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                Pengaturan
            </a>

            <hr class="border-gray-100 my-4">

            <p class="px-4 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-2">Eksternal</p>
            <a href="{{ route('display') }}" target="_blank"
                class="flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-lg text-gray-700 hover:bg-gray-100 transition-colors">
                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                </svg>
                Buka Live Display
                <span class="ml-auto">
                    <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                    </svg>
                </span>
            </a>
        </div>
        
        <!-- Storage Usage (Minimalist) -->
        <div class="px-6 py-2 mb-2">
            @php
                $totalBytes = \App\Models\Media::sum('file_size');
                $totalMB = $totalBytes / 1048576;
                $percent = min(100, ($totalMB / 1024) * 100);
            @endphp
            <div class="flex items-center justify-between text-[10px] text-gray-400 mb-1 uppercase tracking-wider font-semibold">
                <span>Storage</span>
                <span>{{ number_format($totalMB, 1) }} MB</span>
            </div>
            <div class="w-full bg-gray-100 rounded-full h-1 overflow-hidden">
                <div class="bg-gray-400 h-1 rounded-full opacity-60" style="width: {{ $percent }}%"></div>
            </div>
        </div>

        <!-- User Profile (Bottom) -->
        <div class="w-full p-4 border-t border-gray-200 bg-gray-50 mt-auto flex-shrink-0">
            <div class="flex items-center gap-3 mb-4">
                <div
                    class="w-10 h-10 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ Auth::user()->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ Auth::user()->email }}</p>
                </div>
            </div>

            <div class="flex gap-2">
                <a href="{{ route('profile.edit') }}"
                    class="flex-1 text-center px-4 py-2 text-xs font-semibold text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
                    Profile
                </a>
                <form method="POST" action="{{ route('logout') }}" class="flex-1">
                    @csrf
                    <button type="submit"
                        class="w-full text-center px-4 py-2 text-xs font-semibold text-white bg-red-500 rounded-md hover:bg-red-600 transition-colors">
                        Logout
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Backdrop for Mobile -->
    <div x-show="open" @click="open = false" class="fixed inset-0 bg-black/50 z-40 md:hidden backdrop-blur-sm"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" style="display: none;">
    </div>
</nav>