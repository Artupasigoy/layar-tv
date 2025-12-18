<div class="py-12">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">

        <!-- Upload Form -->
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
            <div class="p-6 bg-white border-b border-gray-100">
                <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                    </svg>
                    Upload Media Baru
                </h2>
                <p class="mt-1 text-sm text-gray-500">Tambahkan gambar atau video ke dalam playlist Anda.</p>
            </div>
            <div class="p-6" x-data="{
                file: null,
                fileName: null,
                fileSize: null,
                progress: 0,
                isUploading: false,
                formatSize(bytes) {
                    if (bytes === 0) return '0 Bytes';
                    const k = 1024;
                    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                    const i = Math.floor(Math.log(bytes) / Math.log(k));
                    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
                },
                handleFile(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.file = file;
                        this.fileName = file.name;
                        this.fileSize = this.formatSize(file.size);
                        this.progress = 0;
                        this.isUploading = false;
                    }
                },
                startUpload() {
                    if (!this.file) return;
                    this.isUploading = true;
                    this.progress = 1; // Start progress
                    
                    $wire.upload('file', this.file,
                        () => {
                            // Success: Call the Livewire save method
                            $wire.save().then(() => {
                                this.isUploading = false;
                                this.resetForm();
                            });
                        },
                        () => {
                            // Error
                            this.isUploading = false;
                            this.progress = 0;
                            alert('Gagal mengupload file.');
                        },
                        (event) => {
                            // Progress
                            this.progress = event.detail.progress;
                        }
                    );
                },
                resetForm() {
                    this.file = null;
                    this.fileName = null;
                    this.fileSize = null;
                    this.progress = 0;
                    document.getElementById('file').value = '';
                }
            }">
                <div class="space-y-4">
                    <div>
                        <label for="file" class="block text-sm font-medium text-gray-700">Pilih File
                            (Gambar/Video)</label>

                        <!-- File Drop Area -->
                        <div class="mt-2 flex items-center justify-center w-full">
                            <label for="file"
                                class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer bg-gray-50 hover:bg-gray-100 transition duration-300 relative overflow-hidden">

                                <!-- Default State -->
                                <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!fileName">
                                    <svg class="w-8 h-8 mb-4 text-gray-500" aria-hidden="true"
                                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                            stroke-width="2"
                                            d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                                    </svg>
                                    <p class="mb-2 text-sm text-gray-500"><span class="font-semibold">Klik untuk pilih
                                            file</span></p>
                                    <p class="text-xs text-gray-500">MP4, JPG, PNG, WEBP (Maks. 50MB)</p>
                                </div>

                                <!-- File Selected State -->
                                <div class="flex flex-col items-center justify-center w-full h-full bg-indigo-50"
                                    x-show="fileName" style="display: none;">
                                    <svg class="w-8 h-8 mb-2 text-indigo-500" fill="none" viewBox="0 0 24 24"
                                        stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-sm font-bold text-indigo-700" x-text="fileName"></p>
                                    <p class="text-xs text-indigo-500" x-text="fileSize"></p>
                                    <p class="mt-2 text-xs text-gray-400">Klik untuk ganti file</p>
                                </div>

                                <!-- Hidden Input -->
                                <input id="file" type="file" @change="handleFile"
                                    accept="image/*,video/mp4,video/x-m4v,video/*" class="hidden" />
                            </label>
                        </div>

                        <!-- Progress Bar -->
                        <div x-show="isUploading" class="mt-4" style="display: none;">
                            <div class="flex justify-between mb-1">
                                <span class="text-xs font-medium text-indigo-700">Mengupload...</span>
                                <span class="text-xs font-medium text-indigo-700" x-text="progress + '%'"></span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2.5">
                                <div class="bg-indigo-600 h-2.5 rounded-full transition-all duration-300"
                                    :style="'width: ' + progress + '%'"></div>
                            </div>
                        </div>

                        @error('file') <span class="text-red-500 text-sm block mt-2 text-center">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="flex justify-end">
                        <button type="button" @click="startUpload()" :disabled="!file || isUploading"
                            class="inline-flex justify-center py-2.5 px-6 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!isUploading">Upload Sekarang</span>
                            <span x-show="isUploading">Memproses...</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Media List -->
        <div class="bg-white shadow-lg rounded-2xl border border-gray-100 overflow-hidden">
            <div
                class="px-6 py-5 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4 bg-gray-50">
                <div class="flex items-center gap-4">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                            </svg>
                            Daftar Playlist
                        </h2>
                        <p class="mt-1 text-sm text-gray-500 hidden sm:block">Kelola urutan dan status tayang media.</p>
                    </div>
                </div>
                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-indigo-100 text-indigo-800">
                    Total: {{ $medias->total() }}
                </span>

                <!-- Filters -->
                <div class="flex bg-gray-200 rounded-lg p-1">
                    <button wire:click="setFilter('all')"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $filterStatus === 'all' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Semua
                    </button>
                    <button wire:click="setFilter('active')"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $filterStatus === 'active' ? 'bg-white text-green-700 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Aktif
                    </button>
                    <button wire:click="setFilter('inactive')"
                        class="px-4 py-1.5 rounded-md text-sm font-medium transition-all {{ $filterStatus === 'inactive' ? 'bg-white text-gray-900 shadow-sm' : 'text-gray-500 hover:text-gray-700' }}">
                        Non-Aktif
                    </button>
                </div>
            </div>

            <div class="bg-white">
                <ul role="list" class="divide-y divide-gray-100">
                    @foreach($medias as $index => $media)
                        <li wire:key="{{ $media->id }}"
                            class="p-4 sm:px-6 hover:bg-gray-50 transition duration-150 ease-in-out group">
                            <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
                                <div class="flex items-center min-w-0 gap-4 flex-1 w-full sm:w-auto">
                                    <!-- Numbering -->
                                    <div
                                        class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full bg-gray-100 text-gray-500 font-bold text-sm">
                                        {{ ($medias->currentPage() - 1) * $medias->perPage() + $loop->iteration }}
                                    </div>

                                    <!-- Preview -->
                                    <div wire:click="openPreview({{ $media->id }})"
                                        class="flex-shrink-0 h-16 w-24 bg-gray-100 rounded-lg overflow-hidden shadow-sm border border-gray-200 relative group-hover:shadow-md transition-shadow cursor-pointer">
                                        @if($media->type == 'image')
                                            <img src="{{ asset('storage/' . $media->file_path) }}" alt="{{ $media->title }}"
                                                class="h-full w-full object-cover">
                                        @else
                                            <div class="h-full w-full flex items-center justify-center bg-gray-900 text-white">
                                                <svg class="h-8 w-8 opacity-70" fill="none" viewBox="0 0 24 24"
                                                    stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Info -->
                                    <div class="min-w-0 flex-1 w-full max-w-[200px] sm:max-w-none">
                                        <div class="flex items-center justify-between mr-4">
                                            <div class="min-w-0 flex-1 mr-2"> <!-- Added flex-1 and mr-2 -->
                                                <p
                                                    class="text-base font-semibold text-gray-900 truncate group-hover:text-indigo-600 transition-colors">
                                                    {{ $media->title }}
                                                </p>
                                                <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                    <span
                                                        class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium uppercase {{ $media->type == 'video' ? 'bg-pink-100 text-pink-800' : 'bg-blue-100 text-blue-800' }}">
                                                        {{ $media->type }}
                                                    </span>
                                                    @if($media->file_size)
                                                        <span class="text-xs text-gray-500">
                                                            {{ number_format($media->file_size / 1048576, 2) }} MB
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Toggle Switch -->
                                            <button wire:click="toggleStatus({{ $media->id }})"
                                                class="relative inline-flex flex-shrink-0 h-6 w-11 border-2 border-transparent rounded-full cursor-pointer transition-colors ease-in-out duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 {{ $media->is_active ? 'bg-green-500' : 'bg-gray-200' }}"
                                                role="switch" aria-checked="{{ $media->is_active ? 'true' : 'false' }}">
                                                <span class="sr-only">Use setting</span>
                                                <span aria-hidden="true"
                                                    class="pointer-events-none inline-block h-5 w-5 rounded-full bg-white shadow transform ring-0 transition ease-in-out duration-200 {{ $media->is_active ? 'translate-x-5' : 'translate-x-0' }}"></span>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <!-- Consolidated Actions Menu -->
                                <div class="flex items-center gap-1 sm:gap-3">
                                    <!-- Sorting Buttons -->
                                    <div class="flex flex-col sm:flex-row gap-1">
                                        <button wire:click="moveUp({{ $media->id }})"
                                            class="p-1.5 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            title="Naik">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 15l7-7 7 7" />
                                            </svg>
                                        </button>
                                        <button wire:click="moveDown({{ $media->id }})"
                                            class="p-1.5 rounded-full text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            title="Turun">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 9l-7 7-7-7" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Delete Button -->
                                    <button wire:click="confirmDelete({{ $media->id }})"
                                        class="p-2 ml-1 text-red-400 hover:text-red-700 hover:bg-red-50 rounded-full transition-colors"
                                        title="Hapus">
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>

            @if($medias->isEmpty())
                <div class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Belum ada media</h3>
                    <p class="mt-1 text-sm text-gray-500">Silakan upload gambar atau video untuk memulai.</p>
                </div>
            @endif

            @if($medias->hasPages())
                <div class="px-6 py-4 border-t border-gray-100 bg-gray-50">
                    {{ $medias->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div x-data="{ show: @entangle('showDeleteModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title"
        role="dialog" aria-modal="true" style="display: none;">

        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="$wire.cancelDelete()"
                aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <div
                class="inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div
                        class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Hapus Media?
                        </h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">
                                Apakah Anda yakin ingin menghapus media ini secara permanen? Tindakan ini tidak dapat
                                dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button type="button" wire:click="destroy"
                        class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Ya, Hapus
                    </button>
                    <button type="button" wire:click="cancelDelete"
                        class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Preview Modal -->
    <div x-data="{ show: @entangle('showPreviewModal') }" x-show="show" x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
        style="display: none;">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closePreview"
                aria-hidden="true"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div
                class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                            Preview Media
                        </h3>
                        <button wire:click="closePreview" type="button"
                            class="bg-white rounded-md text-gray-400 hover:text-gray-500 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div class="mt-2 flex justify-center items-center bg-gray-100 rounded-lg p-2 sm:p-4 min-h-[300px]">
                        @if($previewMedia)
                            @if($previewMedia->type == 'image')
                                <img src="{{ asset('storage/' . $previewMedia->file_path) }}" alt="{{ $previewMedia->title }}"
                                    class="max-h-[70vh] w-auto max-w-full object-contain shadow-md">
                            @elseif($previewMedia->type == 'video')
                                <video controls autoplay class="max-h-[70vh] w-auto max-w-full shadow-md">
                                    <source src="{{ asset('storage/' . $previewMedia->file_path) }}" type="video/mp4">
                                    Browser Anda tidak mendukung tag video.
                                </video>
                            @endif
                        @else
                            <div class="text-gray-400">Memuat...</div>
                        @endif
                    </div>
                    @if($previewMedia)
                        <div class="mt-4 text-center">
                            <p class="text-sm text-gray-500">{{ $previewMedia->title }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>