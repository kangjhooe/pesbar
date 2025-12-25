<section class="space-y-6" x-data="{ 
    showWarning: false, 
    confirmText: '',
    confirmCheckbox: false,
    confirmPassword: false,
    deleteText: 'HAPUS AKUN',
    enteredText: ''
}">
    <div class="border border-gray-200 rounded-lg bg-gray-50">
        <button 
            @click="showWarning = !showWarning" 
            class="w-full px-4 py-3 text-left flex items-center justify-between text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-100 transition-colors rounded-lg"
        >
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="font-medium">Zona Berbahaya</span>
            </div>
            <svg 
                class="w-5 h-5 text-gray-400 transition-transform" 
                :class="{ 'rotate-180': showWarning }"
                fill="none" 
                stroke="currentColor" 
                viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>

        <div 
            x-show="showWarning" 
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="px-4 pb-4 space-y-4"
        >
            <div class="pt-2 border-t border-gray-200">
                <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                        </svg>
                        <div>
                            <h3 class="text-sm font-medium text-yellow-800 mb-1">Peringatan Penting</h3>
                            <p class="text-xs text-yellow-700">
                                Menghapus akun adalah tindakan permanen yang tidak dapat dibatalkan. Semua data, artikel, komentar, dan informasi terkait akun Anda akan dihapus secara permanen.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="space-y-4">
                    <!-- Step 1: Acknowledge Warning -->
                    <div>
                        <label class="flex items-start space-x-3 cursor-pointer">
                            <input 
                                type="checkbox" 
                                x-model="confirmCheckbox"
                                class="mt-1 w-4 h-4 text-red-600 border-gray-300 rounded focus:ring-red-500"
                            >
                            <span class="text-sm text-gray-700">
                                Saya memahami bahwa semua data saya akan dihapus secara permanen dan tidak dapat dikembalikan.
                            </span>
                        </label>
                    </div>

                    <!-- Step 2: Type confirmation text -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Ketik <strong class="text-red-600">HAPUS AKUN</strong> untuk konfirmasi:
                        </label>
                        <input 
                            type="text" 
                            x-model="enteredText"
                            placeholder="Ketik HAPUS AKUN"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                            :class="{ 'border-red-300': enteredText && enteredText !== deleteText }"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            <span x-show="enteredText && enteredText !== deleteText" class="text-red-600">
                                Teks tidak sesuai. Harus tepat: <strong>HAPUS AKUN</strong>
                            </span>
                            <span x-show="!enteredText" class="text-gray-500">
                                Anda harus mengetik "HAPUS AKUN" untuk melanjutkan
                            </span>
                        </p>
                    </div>

                    <!-- Step 3: Password confirmation -->
                    <div x-show="enteredText === deleteText && confirmCheckbox" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Masukkan password Anda untuk konfirmasi:
                        </label>
                        <input 
                            type="password" 
                            x-model="confirmPassword"
                            placeholder="Password Anda"
                            class="block w-full border-gray-300 rounded-md shadow-sm focus:border-red-500 focus:ring-red-500 text-sm"
                        >
                        <p class="mt-1 text-xs text-gray-500">
                            Password diperlukan untuk memastikan ini adalah Anda
                        </p>
                    </div>

                    <!-- Delete Button (only enabled when all conditions met) -->
                    <div x-show="enteredText === deleteText && confirmCheckbox && confirmPassword" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 transform translate-y-2"
                         x-transition:enter-end="opacity-100 transform translate-y-0"
                         class="pt-2">
                        <button
                            type="button"
                            @click="$dispatch('open-modal', 'confirm-user-deletion')"
                            class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                            :disabled="!confirmPassword || confirmPassword.length < 1"
                        >
                            <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Lanjutkan ke Konfirmasi Akhir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Confirmation -->
    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="p-6" x-data="{ finalPassword: '' }">
            @csrf
            @method('delete')

            <div class="flex items-center mb-4">
                <div class="flex-shrink-0 w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h2 class="text-lg font-semibold text-gray-900">
                        Konfirmasi Penghapusan Akun
                    </h2>
                </div>
            </div>

            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-4">
                <p class="text-sm text-red-800 font-medium mb-2">Tindakan ini tidak dapat dibatalkan!</p>
                <ul class="text-xs text-red-700 space-y-1 list-disc list-inside">
                    <li>Semua artikel yang Anda buat akan dihapus</li>
                    <li>Semua komentar Anda akan dihapus</li>
                    <li>Profil dan data pribadi akan dihapus</li>
                    <li>Anda tidak akan bisa login lagi</li>
                </ul>
            </div>

            <p class="text-sm text-gray-600 mb-4">
                Masukkan password Anda sekali lagi untuk mengkonfirmasi penghapusan akun secara permanen.
            </p>

            <div class="mb-6">
                <x-input-label for="password" value="{{ __('Password') }}" class="block text-sm font-medium text-gray-700 mb-2" />
                <x-text-input
                    id="password"
                    name="password"
                    type="password"
                    x-model="finalPassword"
                    class="block w-full"
                    placeholder="{{ __('Password') }}"
                    required
                />
                <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
            </div>

            <div class="flex justify-end space-x-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close')">
                    {{ __('Batal') }}
                </x-secondary-button>

                <button
                    type="submit"
                    class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-md transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed"
                    :disabled="!finalPassword"
                >
                    {{ __('Ya, Hapus Akun Saya') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
