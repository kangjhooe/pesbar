@extends('layouts.admin-simple')

@section('title', 'Tambah Polling')

@section('content')
<div class="p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Tambah Polling</h1>
            <p class="text-gray-600 mt-1">Buat polling baru untuk engagement masyarakat</p>
        </div>
        <a href="{{ route('admin.polls.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
        </a>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow">
        <form action="{{ route('admin.polls.store') }}" method="POST" id="poll-form">
            @csrf
            <div class="p-6 space-y-6">
                <!-- Basic Information -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Polling *</label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}" 
                                   class="form-input @error('title') border-red-500 @enderror" 
                                   placeholder="Masukkan judul polling" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                            <textarea name="description" id="description" rows="4" 
                                      class="form-input @error('description') border-red-500 @enderror" 
                                      placeholder="Deskripsi polling (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="poll_type" class="block text-sm font-medium text-gray-700 mb-2">Tipe Polling *</label>
                            <select name="poll_type" id="poll_type" 
                                    class="form-select @error('poll_type') border-red-500 @enderror" required>
                                <option value="">Pilih tipe polling</option>
                                <option value="single" {{ old('poll_type') == 'single' ? 'selected' : '' }}>Pilihan Tunggal</option>
                                <option value="multiple" {{ old('poll_type') == 'multiple' ? 'selected' : '' }}>Pilihan Ganda</option>
                            </select>
                            @error('poll_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Pilihan tunggal: user hanya bisa memilih 1 opsi. Pilihan ganda: user bisa memilih beberapa opsi.</p>
                        </div>

                        <div>
                            <label for="max_votes_per_user" class="block text-sm font-medium text-gray-700 mb-2">Maksimal Pilihan per User *</label>
                            <input type="number" name="max_votes_per_user" id="max_votes_per_user" value="{{ old('max_votes_per_user', 1) }}" 
                                   class="form-input @error('max_votes_per_user') border-red-500 @enderror" 
                                   min="1" max="10" required>
                            @error('max_votes_per_user')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Jumlah maksimal opsi yang bisa dipilih user (1-10)</p>
                        </div>
                    </div>
                </div>

                <!-- Date Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Periode Polling</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Mulai</label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}" 
                                   class="form-input @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Kosongkan jika polling langsung dimulai</p>
                        </div>

                        <div>
                            <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">Tanggal Selesai</label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}" 
                                   class="form-input @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-sm text-gray-500">Kosongkan jika polling tidak terbatas waktu</p>
                        </div>
                    </div>
                </div>

                <!-- Poll Options -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-gray-900">Opsi Polling *</h3>
                        <button type="button" onclick="addOption()" class="btn-sm btn-primary">
                            <i class="fas fa-plus mr-2"></i>
                            Tambah Opsi
                        </button>
                    </div>
                    <p class="text-sm text-gray-600 mb-4">Minimal 2 opsi diperlukan</p>
                    
                    <div id="options-container" class="space-y-4">
                        @if(old('options'))
                            @foreach(old('options') as $index => $option)
                                <div class="option-item border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-700">Opsi {{ $index + 1 }}</h4>
                                        <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks Opsi *</label>
                                            <input type="text" name="options[{{ $index }}][text]" value="{{ $option['text'] ?? '' }}" 
                                                   class="form-input" placeholder="Masukkan teks opsi" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="color" name="options[{{ $index }}][color]" value="{{ $option['color'] ?? '#3B82F6' }}" 
                                                       class="h-10 w-20 rounded border border-gray-300 cursor-pointer" required>
                                                <input type="text" value="{{ $option['color'] ?? '#3B82F6' }}" 
                                                       class="form-input flex-1 color-text-input" placeholder="#3B82F6" 
                                                       onchange="updateColorPicker(this)">
                                            </div>
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                                            <textarea name="options[{{ $index }}][description]" rows="2" 
                                                      class="form-input" placeholder="Deskripsi opsi (opsional)">{{ $option['description'] ?? '' }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <!-- Default 2 options -->
                            @for($i = 0; $i < 2; $i++)
                                <div class="option-item border border-gray-200 rounded-lg p-4">
                                    <div class="flex items-start justify-between mb-3">
                                        <h4 class="text-sm font-medium text-gray-700">Opsi {{ $i + 1 }}</h4>
                                        <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800" {{ $i < 2 ? 'style="display:none;"' : '' }}>
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Teks Opsi *</label>
                                            <input type="text" name="options[{{ $i }}][text]" value="" 
                                                   class="form-input" placeholder="Masukkan teks opsi" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                                            <div class="flex items-center space-x-2">
                                                <input type="color" name="options[{{ $i }}][color]" value="#3B82F6" 
                                                       class="h-10 w-20 rounded border border-gray-300 cursor-pointer color-picker" required>
                                                <input type="text" value="#3B82F6" 
                                                       class="form-input flex-1 color-text-input" placeholder="#3B82F6" 
                                                       onchange="updateColorPicker(this)">
                                            </div>
                                        </div>
                                        <div class="lg:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                                            <textarea name="options[{{ $i }}][description]" rows="2" 
                                                      class="form-input" placeholder="Deskripsi opsi (opsional)"></textarea>
                                        </div>
                                    </div>
                                </div>
                            @endfor
                        @endif
                    </div>
                    @error('options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('options.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Settings -->
                <div>
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Pengaturan</h3>
                    <div class="space-y-4">
                        <div class="flex items-center">
                            <input type="checkbox" name="is_active" id="is_active" value="1" 
                                   {{ old('is_active', true) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="is_active" class="ml-2 block text-sm text-gray-900">
                                Polling Aktif
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="allow_anonymous" id="allow_anonymous" value="1" 
                                   {{ old('allow_anonymous', false) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="allow_anonymous" class="ml-2 block text-sm text-gray-900">
                                Izinkan Voting Anonim
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="show_results" id="show_results" value="1" 
                                   {{ old('show_results', true) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_results" class="ml-2 block text-sm text-gray-900">
                                Tampilkan Hasil Polling
                            </label>
                        </div>

                        <div class="flex items-center">
                            <input type="checkbox" name="show_vote_count" id="show_vote_count" value="1" 
                                   {{ old('show_vote_count', true) ? 'checked' : '' }} 
                                   class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                            <label for="show_vote_count" class="ml-2 block text-sm text-gray-900">
                                Tampilkan Jumlah Suara
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end space-x-3">
                <a href="{{ route('admin.polls.index') }}" class="btn-secondary">
                    Batal
                </a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>
                    Simpan Polling
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let optionIndex = {{ old('options') ? count(old('options')) : 2 }};

function addOption() {
    const container = document.getElementById('options-container');
    const optionItem = document.createElement('div');
    optionItem.className = 'option-item border border-gray-200 rounded-lg p-4';
    
    const optionNumber = container.children.length + 1;
    const colors = ['#3B82F6', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
    const color = colors[optionIndex % colors.length];
    
    optionItem.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <h4 class="text-sm font-medium text-gray-700">Opsi ${optionNumber}</h4>
            <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Teks Opsi *</label>
                <input type="text" name="options[${optionIndex}][text]" value="" 
                       class="form-input" placeholder="Masukkan teks opsi" required>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Warna *</label>
                <div class="flex items-center space-x-2">
                    <input type="color" name="options[${optionIndex}][color]" value="${color}" 
                           class="h-10 w-20 rounded border border-gray-300 cursor-pointer color-picker" required>
                    <input type="text" value="${color}" 
                           class="form-input flex-1 color-text-input" placeholder="#3B82F6" 
                           onchange="updateColorPicker(this)">
                </div>
            </div>
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-2">Deskripsi (Opsional)</label>
                <textarea name="options[${optionIndex}][description]" rows="2" 
                          class="form-input" placeholder="Deskripsi opsi (opsional)"></textarea>
            </div>
        </div>
    `;
    
    container.appendChild(optionItem);
    optionIndex++;
    
    // Update option numbers
    updateOptionNumbers();
    
    // Setup color picker sync
    setupColorPickerSync(optionItem);
}

function removeOption(button) {
    const container = document.getElementById('options-container');
    if (container.children.length <= 2) {
        alert('Minimal 2 opsi diperlukan');
        return;
    }
    
    button.closest('.option-item').remove();
    updateOptionNumbers();
}

function updateOptionNumbers() {
    const container = document.getElementById('options-container');
    const items = container.querySelectorAll('.option-item');
    items.forEach((item, index) => {
        const title = item.querySelector('h4');
        if (title) {
            title.textContent = `Opsi ${index + 1}`;
        }
        
        // Show/hide delete button based on count
        const deleteBtn = item.querySelector('button[onclick="removeOption(this)"]');
        if (deleteBtn) {
            deleteBtn.style.display = items.length > 2 ? '' : 'none';
        }
    });
}

function updateColorPicker(textInput) {
    const colorPicker = textInput.previousElementSibling;
    if (colorPicker && colorPicker.type === 'color') {
        const color = textInput.value;
        if (/^#[0-9A-F]{6}$/i.test(color)) {
            colorPicker.value = color;
        }
    }
}

function setupColorPickerSync(container) {
    const colorPickers = container.querySelectorAll('.color-picker');
    const colorTextInputs = container.querySelectorAll('.color-text-input');
    
    colorPickers.forEach(picker => {
        picker.addEventListener('input', function() {
            const textInput = this.nextElementSibling;
            if (textInput && textInput.classList.contains('color-text-input')) {
                textInput.value = this.value;
            }
        });
    });
    
    colorTextInputs.forEach(textInput => {
        textInput.addEventListener('input', function() {
            const colorPicker = this.previousElementSibling;
            if (colorPicker && colorPicker.type === 'color') {
                const color = this.value;
                if (/^#[0-9A-F]{6}$/i.test(color)) {
                    colorPicker.value = color;
                }
            }
        });
    });
}

// Setup color picker sync for existing options
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('options-container');
    if (container) {
        setupColorPickerSync(container);
    }
    
    // Validate form before submit
    document.getElementById('poll-form').addEventListener('submit', function(e) {
        const container = document.getElementById('options-container');
        const optionItems = container.querySelectorAll('.option-item');
        
        if (optionItems.length < 2) {
            e.preventDefault();
            alert('Minimal 2 opsi diperlukan');
            return false;
        }
        
        // Validate each option
        let isValid = true;
        optionItems.forEach((item, index) => {
            const textInput = item.querySelector('input[name*="[text]"]');
            const colorInput = item.querySelector('input[name*="[color]"]');
            
            if (!textInput.value.trim()) {
                isValid = false;
                textInput.focus();
            }
            
            if (!colorInput.value) {
                isValid = false;
            }
        });
        
        if (!isValid) {
            e.preventDefault();
            alert('Harap lengkapi semua field yang wajib diisi');
            return false;
        }
    });
    
    // Auto-update max_votes_per_user based on poll_type
    const pollTypeSelect = document.getElementById('poll_type');
    const maxVotesInput = document.getElementById('max_votes_per_user');
    
    if (pollTypeSelect && maxVotesInput) {
        pollTypeSelect.addEventListener('change', function() {
            if (this.value === 'single') {
                maxVotesInput.value = 1;
                maxVotesInput.setAttribute('readonly', 'readonly');
            } else {
                maxVotesInput.removeAttribute('readonly');
            }
        });
        
        // Set initial state
        if (pollTypeSelect.value === 'single') {
            maxVotesInput.value = 1;
            maxVotesInput.setAttribute('readonly', 'readonly');
        }
    }
    
    // Validate end_date is after start_date
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    
    if (startDateInput && endDateInput) {
        startDateInput.addEventListener('change', function() {
            if (this.value && endDateInput.value && endDateInput.value <= this.value) {
                endDateInput.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
            } else {
                endDateInput.setCustomValidity('');
            }
        });
        
        endDateInput.addEventListener('change', function() {
            if (startDateInput.value && this.value && this.value <= startDateInput.value) {
                this.setCustomValidity('Tanggal selesai harus setelah tanggal mulai');
            } else {
                this.setCustomValidity('');
            }
        });
    }
});
</script>
@endsection

