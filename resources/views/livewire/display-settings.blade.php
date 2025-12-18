<div class="py-12">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Pengaturan
                </h2>
                <p class="mt-1 text-sm text-gray-500">Sesuaikan pengaturan aplikasi signage Anda di sini.</p>
            </div>

            <div class="p-8">
                <form wire:submit.prevent="save" class="space-y-8">

                    <!-- Logo Branding -->
                    <div class="bg-gray-50 p-6 rounded-xl border border-gray-100">
                        <label class="block text-base font-semibold text-gray-900 mb-4">Logo Branding</label>
                        <div class="flex flex-col sm:flex-row items-center gap-6">
                            @if ($logo)
                                <div class="relative flex-shrink-0">
                                    <img src="{{ $logo->temporaryUrl() }}"
                                        class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md">
                                </div>
                            @elseif ($existingLogo)
                                <div class="relative group flex-shrink-0">
                                    <img src="{{ asset('storage/' . $existingLogo) }}"
                                        class="h-24 w-24 rounded-full object-cover border-4 border-white shadow-md">
                                    <button type="button" wire:click="deleteLogo"
                                        class="absolute -top-2 -right-2 bg-red-600 text-white rounded-full p-1.5 shadow-lg hover:bg-red-700 transition-colors transform hover:scale-110"
                                        title="Hapus Logo">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            @else
                                <span
                                    class="h-24 w-24 rounded-full overflow-hidden bg-white flex flex-col items-center justify-center border-2 border-dashed border-gray-300 flex-shrink-0">
                                    <span class="text-xs text-gray-400 font-medium">Tanpa Logo</span>
                                </span>
                            @endif

                            <div class="w-full">
                                <input wire:model="logo" type="file"
                                    accept="image/png, image/jpeg, image/jpg, image/webp" class="block w-full text-sm text-gray-500
                                    file:mr-4 file:py-2.5 file:px-6
                                    file:rounded-full file:border-0
                                    file:text-sm file:font-semibold
                                    file:bg-indigo-600 file:text-white
                                    file:shadow-sm
                                    hover:file:bg-indigo-700
                                    file:transition file:duration-200">
                                <p class="mt-2 text-xs text-gray-500 text-center sm:text-left">Format: PNG, JPG, WEBP.
                                    Ukuran Maksimal: 1 MB.</p>
                            </div>
                        </div>
                        @error('logo') <span class="text-red-500 text-sm mt-2 block">{{ $message }}</span> @enderror
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Clock Toggle -->
                        <div
                            class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-xl hover:border-indigo-200 hover:shadow-sm transition-all">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">Jam Digital</span>
                                <span class="text-xs text-gray-500 mt-1">Tampilkan waktu di pojok layar</span>
                            </div>
                            <!-- Toggle Button -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="show_clock" type="checkbox" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </label>
                        </div>

                        <!-- Animation Toggle -->
                        <div
                            class="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-xl hover:border-indigo-200 hover:shadow-sm transition-all">
                            <div class="flex flex-col">
                                <span class="font-bold text-gray-900">Animasi "Ken Burns"</span>
                                <span class="text-xs text-gray-500 mt-1">Efek zoom perlahan pada gambar</span>
                            </div>
                            <!-- Toggle Button -->
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input wire:model="enable_animation" type="checkbox" class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600">
                                </div>
                            </label>
                        </div>
                    </div>

                    <!-- Image Duration -->
                    <div>
                        <label for="image_duration" class="block text-sm font-medium text-gray-700 mb-1">Durasi Ganti
                            Gambar</label>
                        <div class="relative rounded-md shadow-sm">
                            <input wire:model="image_duration" type="number" id="image_duration" min="3"
                                class="block w-full pr-12 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-3"
                                placeholder="10">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Detik</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Berapa lama setiap gambar ditampilkan sebelum berganti.
                        </p>
                        @error('image_duration') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Max File Size -->
                    <div>
                        <label for="max_file_size" class="block text-sm font-medium text-gray-700 mb-1">Maksimal Ukuran
                            File Upload</label>
                        <div class="relative rounded-md shadow-sm">
                            <input wire:model="max_file_size" type="number" id="max_file_size" min="1" max="500"
                                class="block w-full pr-12 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-3"
                                placeholder="50">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">MB</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Ukuran maksimal file yang dapat diupload (gambar dan
                            video). Maks: 500 MB.
                        </p>
                        @error('max_file_size') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <!-- Auto Reload Interval -->
                    <div>
                        <label for="auto_reload_interval" class="block text-sm font-medium text-gray-700 mb-1">Interval Auto-Reload Signage</label>
                        <div class="relative rounded-md shadow-sm">
                            <input wire:model="auto_reload_interval" type="number" id="auto_reload_interval" min="1" max="24"
                                class="block w-full pr-12 border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm py-3"
                                placeholder="6">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 sm:text-sm">Jam</span>
                            </div>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Layar signage akan refresh otomatis untuk memuat konten terbaru. Maks: 24 jam.</p>
                        @error('auto_reload_interval') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="pt-4 border-t border-gray-100 flex items-center justify-end gap-3">
                        <div x-data="{ shown: false, timeout: null }"
                            x-init="@this.on('saved', () => { shown = true; clearTimeout(timeout); timeout = setTimeout(() => { shown = false }, 2000); })"
                            x-show.transition.out.opacity.duration.1500ms="shown"
                            x-transition:leave.opacity.duration.1500ms
                            class="text-sm text-green-600 font-medium flex items-center gap-1" style="display: none;">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7" />
                            </svg>
                            Tersimpan
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                            <span wire:loading.remove>Simpan Pengaturan</span>
                            <span wire:loading>Menyimpan...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>