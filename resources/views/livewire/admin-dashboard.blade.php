<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <!-- Total Media -->
            <div
                class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100 transform hover:scale-105 transition duration-300">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600 mr-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Total Media</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $totalMedia }} File</div>
                    </div>
                </div>
            </div>

            <!-- Active Media -->
            <div
                class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100 transform hover:scale-105 transition duration-300">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Media Aktif</div>
                        <div class="text-2xl font-bold text-gray-900">{{ $activeMedia }} Tayang</div>
                    </div>
                </div>
            </div>

            <!-- Clock Status -->
            <div
                class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100 transform hover:scale-105 transition duration-300">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Jam Digital</div>
                        <div class="text-xl font-bold mt-1">
                            @if($setting->show_clock)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Aktif
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Non-Aktif
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logo Status -->
            <div
                class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100 transform hover:scale-105 transition duration-300">
                <div class="p-6 flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-sm font-medium text-gray-500">Logo</div>
                        <div class="text-xl font-bold mt-1">
                            @if($setting->logo_path)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    Terpasang
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                    Default
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Preview Section -->
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-100">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-gray-900">Live Preview</h3>
                        <p class="text-sm text-gray-500">Tampilan real-time layar signage Anda.</p>
                    </div>
                    <a href="{{ route('display') }}" target="_blank"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 rounded-lg font-medium hover:bg-indigo-100 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                        </svg>
                        Buka Layar Penuh
                    </a>
                </div>

                <!-- TV Frame Container -->
                <div class="max-w-4xl mx-auto">
                    <div
                        class="relative mx-auto border-gray-800 bg-gray-800 border-[8px] rounded-t-xl h-fit shadow-2xl">
                        <div class="rounded-lg overflow-hidden bg-black aspect-video relative">
                            <!-- Helper Text if Loading/Empty -->
                            <div class="absolute inset-0 flex items-center justify-center text-gray-500 z-0">
                                <span class="animate-pulse">Memuat Preview...</span>
                            </div>

                            <!-- The Frame -->
                            <iframe src="{{ route('display', ['muted' => 1]) }}"
                                class="w-full h-full border-0 relative z-10" allow="autoplay"></iframe>

                            <!-- Glare Effect (Subtle) -->
                            <div
                                class="absolute inset-0 bg-gradient-to-tr from-white/5 to-transparent pointer-events-none z-20">
                            </div>
                        </div>
                    </div>
                    <!-- TV Stand -->
                    <div
                        class="relative mx-auto bg-gray-900 rounded-b-xl h-[20px] max-w-[95%] shadow-md flex justify-center items-center">
                        <div class="w-1 h-1 bg-green-500 rounded-full shadow-[0_0_5px_rgba(34,197,94,0.8)]"></div>
                    </div>
                    <!-- Base -->
                    <div class="mx-auto bg-gray-800 h-[10px] w-[30%] rounded-b-lg opacity-50 shadow-xl"></div>
                </div>
            </div>
        </div>
    </div>
</div>