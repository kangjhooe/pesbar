@extends('layouts.user')

@section('content')
<div class="max-w-2xl mx-auto">
        <div class="bg-white rounded-lg shadow-lg p-8">
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            @if(session('info'))
                <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded mb-6">
                    {{ session('info') }}
                </div>
            @endif

            <div class="text-center mb-8">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-user-plus text-2xl text-blue-600"></i>
                </div>
                <h1 class="text-2xl font-bold text-gray-900 mb-2">Ajukan Permintaan Menjadi Penulis</h1>
                <p class="text-gray-600">Lengkapi informasi di bawah ini untuk mengajukan permintaan menjadi penulis</p>
            </div>

            <form method="POST" action="{{ route('user.submit-upgrade-request') }}" enctype="multipart/form-data" class="space-y-6" id="upgradeForm">
                @csrf
                
                <!-- Type Selection Section -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-6 mb-6">
                    <label class="block text-sm font-semibold text-gray-900 mb-4">
                        Pilih Tipe Akun <span class="text-red-500">*</span>
                    </label>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors" id="perorangan-label">
                            <input type="radio" 
                                   name="verification_type" 
                                   value="perorangan" 
                                   required
                                   class="sr-only peer"
                                   {{ old('verification_type') === 'perorangan' ? 'checked' : '' }}
                                   onchange="toggleDocumentFields()">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-user text-2xl text-blue-600 mr-3"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Perorangan</div>
                                        <div class="text-sm text-gray-600">Untuk individu yang ingin menjadi penulis</div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dokumen yang diperlukan: KTP
                                </div>
                            </div>
                            <div class="ml-4 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-blue-600 peer-checked:bg-blue-600 flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                            </div>
                        </label>
                        
                        <label class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer hover:bg-blue-50 transition-colors" id="lembaga-label">
                            <input type="radio" 
                                   name="verification_type" 
                                   value="lembaga" 
                                   required
                                   class="sr-only peer"
                                   {{ old('verification_type') === 'lembaga' ? 'checked' : '' }}
                                   onchange="toggleDocumentFields()">
                            <div class="flex-1">
                                <div class="flex items-center mb-2">
                                    <i class="fas fa-building text-2xl text-green-600 mr-3"></i>
                                    <div>
                                        <div class="font-semibold text-gray-900">Lembaga</div>
                                        <div class="text-sm text-gray-600">Untuk organisasi/lembaga yang ingin menjadi penulis</div>
                                    </div>
                                </div>
                                <div class="text-xs text-gray-500 mt-2">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Dokumen yang diperlukan: Izin Operasional / SK Pendirian
                                </div>
                            </div>
                            <div class="ml-4 w-5 h-5 border-2 border-gray-300 rounded-full peer-checked:border-green-600 peer-checked:bg-green-600 flex items-center justify-center">
                                <i class="fas fa-check text-white text-xs hidden peer-checked:block"></i>
                            </div>
                        </label>
                    </div>
                    @error('verification_type')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Document Upload Section -->
                <div id="document-section" class="hidden">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 mb-6">
                        <h3 class="text-sm font-semibold text-gray-900 mb-4">
                            <i class="fas fa-file-upload mr-2"></i>
                            <span id="document-title">Unggah Dokumen Identitas</span>
                        </h3>
                        <label for="verification_document" class="block text-sm font-medium text-gray-700 mb-2">
                            <span id="document-label-text">Upload Dokumen</span> <span class="text-red-500">*</span>
                        </label>
                        <div class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors" id="upload-area">
                            <input type="file" 
                                   name="verification_document" 
                                   id="verification_document" 
                                   accept=".pdf,.jpg,.jpeg,.png"
                                   class="hidden"
                                   onchange="handleFileSelect(this)">
                            <label for="verification_document" class="cursor-pointer">
                                <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                                <p class="text-sm text-gray-600 mb-1">
                                    <span id="upload-text" class="text-blue-600 font-medium">Klik untuk upload</span> atau drag and drop
                                </p>
                                <p class="text-xs text-gray-500">Format: PDF, JPG, PNG. Maksimal 5MB</p>
                            </label>
                            <div id="file-name" class="mt-2 text-sm text-gray-700 hidden"></div>
                        </div>
                        <p class="mt-2 text-xs text-gray-600" id="document-hint">
                            <i class="fas fa-info-circle mr-1"></i>
                            <span id="document-hint-text">Pastikan dokumen jelas terbaca dan masih berlaku</span>
                        </p>
                        @error('verification_document')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                <!-- Bio Section -->
                <div>
                    <label for="bio" class="block text-sm font-medium text-gray-700 mb-2">
                        Biografi <span class="text-red-500">*</span>
                    </label>
                    <textarea name="bio" 
                              id="bio" 
                              rows="4" 
                              required
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('bio') border-red-500 @enderror"
                              placeholder="Ceritakan tentang diri Anda, pengalaman menulis, dan mengapa Anda ingin menjadi penulis...">{{ old('bio') }}</textarea>
                    @error('bio')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Avatar Section -->
                <div>
                    <label for="avatar" class="block text-sm font-medium text-gray-700 mb-2">
                        Foto Profil
                    </label>
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-20 h-20 bg-gray-200 rounded-full flex items-center justify-center">
                                <i class="fas fa-user text-gray-400 text-2xl"></i>
                            </div>
                        </div>
                        <div class="flex-1">
                            <input type="file" 
                                   name="avatar" 
                                   id="avatar" 
                                   accept="image/*"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('avatar') border-red-500 @enderror">
                            <p class="mt-1 text-sm text-gray-500">Format: JPG, PNG, GIF. Maksimal 2MB</p>
                            @error('avatar')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Website Section -->
                <div>
                    <label for="website" class="block text-sm font-medium text-gray-700 mb-2">
                        Website/Blog (Opsional)
                    </label>
                    <input type="url" 
                           name="website" 
                           id="website" 
                           value="{{ old('website') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('website') border-red-500 @enderror"
                           placeholder="https://example.com">
                    @error('website')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Location Section -->
                <div>
                    <label for="location" class="block text-sm font-medium text-gray-700 mb-2">
                        Lokasi (Opsional)
                    </label>
                    <input type="text" 
                           name="location" 
                           id="location" 
                           value="{{ old('location') }}"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent @error('location') border-red-500 @enderror"
                           placeholder="Kabupaten Pesisir Barat, Lampung">
                    @error('location')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Social Links Section -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Media Sosial (Opsional)
                    </label>
                    <div class="space-y-3">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                                <i class="fab fa-facebook-f text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[facebook]" 
                                   value="{{ old('social_links.facebook') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://facebook.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-400 rounded-full flex items-center justify-center">
                                <i class="fab fa-twitter text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[twitter]" 
                                   value="{{ old('social_links.twitter') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://twitter.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-pink-600 rounded-full flex items-center justify-center">
                                <i class="fab fa-instagram text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[instagram]" 
                                   value="{{ old('social_links.instagram') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://instagram.com/username">
                        </div>
                        
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-blue-800 rounded-full flex items-center justify-center">
                                <i class="fab fa-linkedin text-white text-sm"></i>
                            </div>
                            <input type="url" 
                                   name="social_links[linkedin]" 
                                   value="{{ old('social_links.linkedin') }}"
                                   class="flex-1 px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="https://linkedin.com/in/username">
                        </div>
                    </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="bg-gray-50 rounded-lg p-4">
                    <h3 class="text-sm font-medium text-gray-900 mb-2">Syarat dan Ketentuan</h3>
                    <ul class="text-sm text-gray-600 space-y-1">
                        <li>• Anda harus menyetujui untuk menulis konten yang berkualitas dan sesuai dengan etika jurnalistik</li>
                        <li>• Semua artikel akan melalui proses review sebelum dipublikasikan</li>
                        <li>• Admin berhak menolak atau meminta revisi artikel yang tidak sesuai standar</li>
                        <li>• Anda bertanggung jawab atas keaslian dan keakuratan konten yang ditulis</li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <div class="flex items-center justify-between pt-6">
                    <a href="{{ route('dashboard') }}" 
                       class="text-gray-600 hover:text-gray-800 font-medium transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>
                        Kembali
                    </a>
                    
                    <button type="submit" 
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors">
                        <i class="fas fa-paper-plane mr-2"></i>
                        Ajukan Permintaan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function toggleDocumentFields() {
    const perorangan = document.querySelector('input[name="verification_type"][value="perorangan"]');
    const lembaga = document.querySelector('input[name="verification_type"][value="lembaga"]');
    const documentSection = document.getElementById('document-section');
    const documentTitle = document.getElementById('document-title');
    const documentLabelText = document.getElementById('document-label-text');
    const documentHintText = document.getElementById('document-hint-text');
    const uploadText = document.getElementById('upload-text');
    const uploadArea = document.getElementById('upload-area');
    const verificationDocument = document.getElementById('verification_document');
    
    if (perorangan.checked) {
        documentSection.classList.remove('hidden');
        documentTitle.textContent = 'Unggah KTP';
        documentLabelText.textContent = 'Upload KTP';
        documentHintText.textContent = 'Pastikan KTP jelas terbaca dan masih berlaku';
        uploadText.textContent = 'Klik untuk upload';
        uploadText.className = 'text-blue-600 font-medium';
        uploadArea.className = 'border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-blue-400 transition-colors';
        verificationDocument.setAttribute('required', 'required');
    } else if (lembaga.checked) {
        documentSection.classList.remove('hidden');
        documentTitle.textContent = 'Unggah Izin Operasional / SK Pendirian';
        documentLabelText.textContent = 'Upload Izin Operasional / SK Pendirian';
        documentHintText.textContent = 'Pastikan dokumen resmi dan masih berlaku';
        uploadText.textContent = 'Klik untuk upload';
        uploadText.className = 'text-green-600 font-medium';
        uploadArea.className = 'border-2 border-dashed border-gray-300 rounded-lg p-6 text-center hover:border-green-400 transition-colors';
        verificationDocument.setAttribute('required', 'required');
    } else {
        documentSection.classList.add('hidden');
        verificationDocument.removeAttribute('required');
    }
}

function handleFileSelect(input) {
    const fileNameDiv = document.getElementById('file-name');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
        fileNameDiv.textContent = `File terpilih: ${file.name} (${fileSize} MB)`;
        fileNameDiv.classList.remove('hidden');
        
        // Validate file size (5MB max)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran file terlalu besar. Maksimal 5MB.');
            input.value = '';
            fileNameDiv.classList.add('hidden');
            return;
        }
    } else {
        fileNameDiv.classList.add('hidden');
    }
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleDocumentFields();
    
    // Add visual feedback for radio selection
    const radios = document.querySelectorAll('input[name="verification_type"]');
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Update label styles
            document.getElementById('perorangan-label').classList.remove('border-blue-500', 'bg-blue-50');
            document.getElementById('lembaga-label').classList.remove('border-green-500', 'bg-green-50');
            
            if (this.value === 'perorangan') {
                document.getElementById('perorangan-label').classList.add('border-blue-500', 'bg-blue-50');
            } else {
                document.getElementById('lembaga-label').classList.add('border-green-500', 'bg-green-50');
            }
        });
        
        // Set initial state
        if (radio.checked) {
            radio.dispatchEvent(new Event('change'));
        }
    });
});
</script>

<style>
input[type="radio"]:checked + div {
    border-color: currentColor;
}
</style>
@endsection