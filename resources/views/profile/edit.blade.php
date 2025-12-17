<x-app-layout>


    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            <!-- Update Profile Information -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        Informasi Profil
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Perbarui informasi profil akun dan alamat email Anda.</p>
                </div>
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-profile-information-form')
                    </div>
                </div>
            </div>

            <!-- Update Password -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                <div class="p-6 bg-white border-b border-gray-100">
                    <h2 class="text-xl font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Perbarui Kata Sandi
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Pastikan akun Anda menggunakan kata sandi yang panjang dan
                        acak
                        agar tetap aman.</p>
                </div>
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.update-password-form')
                    </div>
                </div>
            </div>

            <!-- Delete Account -->
            <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-red-100">
                <div class="p-6 bg-white border-b border-gray-100">
                    <h2 class="text-xl font-bold text-red-600 flex items-center gap-2">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Hapus Akun
                    </h2>
                    <p class="mt-1 text-sm text-gray-500">Setelah akun dihapus, semua data dan sumber daya akan dihapus
                        secara permanen.</p>
                </div>
                <div class="p-6">
                    <div class="max-w-xl">
                        @include('profile.partials.delete-user-form')
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>