@extends('layouts.admin-simple')

@section('title', 'Tambah Polling')
@section('page-title', 'Tambah Polling')
@section('page-subtitle', 'Buat polling baru untuk sidebar publik')

@section('content')
<div class="max-w-4xl space-y-4">
    @include('admin.partials.widget-placement-note', [
        'items' => [
            'Sidebar beranda — setelah widget agenda',
            'Sidebar halaman artikel (versi ringkas)',
        ],
    ])

    <div class="bg-white border border-news-line overflow-hidden">
        <div class="px-6 py-4 border-b border-news-line flex flex-wrap items-center justify-between gap-3">
            <div>
                <h3 class="text-lg font-semibold text-news-ink">Form Polling</h3>
                <p class="text-xs text-news-muted mt-0.5">Lengkapi informasi, opsi, dan pengaturan tampilan</p>
            </div>
            <a href="{{ route('admin.polls.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Kembali
            </a>
        </div>

        <form action="{{ route('admin.polls.store') }}" method="POST" id="poll-form">
            @csrf

            <div class="p-6 space-y-8">
                @if($errors->any())
                    <div class="border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700">
                        <p class="font-semibold mb-1">Periksa kembali formulir</p>
                        <ul class="list-disc list-inside space-y-0.5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Informasi dasar --}}
                <section>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-news-line">
                        <span class="inline-flex h-6 w-6 items-center justify-center bg-news-ink text-white text-[10px] font-bold">1</span>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-news-ink">Informasi Dasar</h4>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div class="lg:col-span-2">
                            <label for="title" class="block text-sm font-medium text-news-ink mb-1.5">
                                Judul Polling <span class="text-news-accent">*</span>
                            </label>
                            <input type="text" name="title" id="title" value="{{ old('title') }}"
                                   class="form-input @error('title') border-red-500 @enderror"
                                   placeholder="Contoh: Siapa calon bupati favorit Anda?" required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="lg:col-span-2">
                            <label for="description" class="block text-sm font-medium text-news-ink mb-1.5">Deskripsi</label>
                            <textarea name="description" id="description" rows="3"
                                      class="form-input @error('description') border-red-500 @enderror"
                                      placeholder="Konteks singkat polling (opsional)">{{ old('description') }}</textarea>
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="poll_type" class="block text-sm font-medium text-news-ink mb-1.5">
                                Tipe Polling <span class="text-news-accent">*</span>
                            </label>
                            <select name="poll_type" id="poll_type"
                                    class="form-select @error('poll_type') border-red-500 @enderror" required>
                                <option value="">Pilih tipe</option>
                                <option value="single" {{ old('poll_type') == 'single' ? 'selected' : '' }}>Pilihan Tunggal</option>
                                <option value="multiple" {{ old('poll_type') == 'multiple' ? 'selected' : '' }}>Pilihan Ganda</option>
                            </select>
                            @error('poll_type')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-news-muted">Tunggal: 1 opsi. Ganda: beberapa opsi.</p>
                        </div>

                        <div>
                            <label for="max_votes_per_user" class="block text-sm font-medium text-news-ink mb-1.5">
                                Maks. Pilihan per User <span class="text-news-accent">*</span>
                            </label>
                            <input type="number" name="max_votes_per_user" id="max_votes_per_user"
                                   value="{{ old('max_votes_per_user', 1) }}"
                                   class="form-input @error('max_votes_per_user') border-red-500 @enderror"
                                   min="1" max="10" required>
                            @error('max_votes_per_user')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-news-muted">Otomatis 1 jika tipe tunggal.</p>
                        </div>
                    </div>
                </section>

                {{-- Periode --}}
                <section>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-news-line">
                        <span class="inline-flex h-6 w-6 items-center justify-center bg-news-ink text-white text-[10px] font-bold">2</span>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-news-ink">Periode Polling</h4>
                    </div>

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
                        <div>
                            <label for="start_date" class="block text-sm font-medium text-news-ink mb-1.5">Tanggal Mulai</label>
                            <input type="datetime-local" name="start_date" id="start_date" value="{{ old('start_date') }}"
                                   class="form-input @error('start_date') border-red-500 @enderror">
                            @error('start_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-news-muted">Kosongkan = langsung mulai</p>
                        </div>
                        <div>
                            <label for="end_date" class="block text-sm font-medium text-news-ink mb-1.5">Tanggal Selesai</label>
                            <input type="datetime-local" name="end_date" id="end_date" value="{{ old('end_date') }}"
                                   class="form-input @error('end_date') border-red-500 @enderror">
                            @error('end_date')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-news-muted">Kosongkan = tanpa batas waktu</p>
                        </div>
                    </div>
                </section>

                {{-- Opsi --}}
                <section>
                    <div class="flex flex-wrap items-center justify-between gap-3 mb-4 pb-2 border-b border-news-line">
                        <div class="flex items-center gap-2">
                            <span class="inline-flex h-6 w-6 items-center justify-center bg-news-ink text-white text-[10px] font-bold">3</span>
                            <h4 class="text-sm font-bold uppercase tracking-wider text-news-ink">
                                Opsi Polling <span class="text-news-accent">*</span>
                                <span class="ml-1 font-normal normal-case tracking-normal text-news-muted">· minimal 2</span>
                            </h4>
                        </div>
                        <button type="button" onclick="addOption()" class="btn-sm btn-primary">
                            <i class="fas fa-plus mr-1.5"></i>Tambah Opsi
                        </button>
                    </div>

                    <div class="border border-news-line overflow-hidden">
                        <div class="hidden sm:flex items-center gap-2 px-3 py-2 bg-news-paper border-b border-news-line text-[10px] font-bold uppercase tracking-wider text-news-muted">
                            <span class="w-8 shrink-0">#</span>
                            <span class="flex-1">Teks / deskripsi</span>
                            <span class="w-12 shrink-0 text-center">Warna</span>
                            <span class="w-10 shrink-0"></span>
                        </div>

                        <div id="options-container" class="divide-y divide-news-line">
                            @if(old('options'))
                                @foreach(old('options') as $index => $option)
                                    <div class="option-item p-3">
                                        <div class="flex items-start gap-2">
                                            <span class="option-badge mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center bg-news-ink text-white text-xs font-bold">{{ $index + 1 }}</span>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <h4 class="sr-only">Opsi {{ $index + 1 }}</h4>
                                                <input type="text" name="options[{{ $index }}][text]" value="{{ $option['text'] ?? '' }}"
                                                       class="form-input" placeholder="Teks opsi *" required>
                                                <input type="text" name="options[{{ $index }}][description]" value="{{ $option['description'] ?? '' }}"
                                                       class="form-input text-xs" placeholder="Deskripsi (opsional)">
                                            </div>
                                            <div class="shrink-0 mt-1">
                                                <input type="color" name="options[{{ $index }}][color]" value="{{ $option['color'] ?? '#b91c1c' }}"
                                                       class="h-10 w-12 border border-news-line cursor-pointer color-picker bg-white p-0.5" required
                                                       title="Warna opsi">
                                                <input type="hidden" class="color-text-input" value="{{ $option['color'] ?? '#b91c1c' }}">
                                            </div>
                                            <button type="button" onclick="removeOption(this)"
                                                    class="mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center text-news-muted hover:text-red-600 transition-colors"
                                                    title="Hapus opsi">
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                @for($i = 0; $i < 2; $i++)
                                    <div class="option-item p-3">
                                        <div class="flex items-start gap-2">
                                            <span class="option-badge mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center bg-news-ink text-white text-xs font-bold">{{ $i + 1 }}</span>
                                            <div class="min-w-0 flex-1 space-y-2">
                                                <h4 class="sr-only">Opsi {{ $i + 1 }}</h4>
                                                <input type="text" name="options[{{ $i }}][text]" value=""
                                                       class="form-input" placeholder="Teks opsi *" required>
                                                <input type="text" name="options[{{ $i }}][description]" value=""
                                                       class="form-input text-xs" placeholder="Deskripsi (opsional)">
                                            </div>
                                            <div class="shrink-0 mt-1">
                                                <input type="color" name="options[{{ $i }}][color]" value="#b91c1c"
                                                       class="h-10 w-12 border border-news-line cursor-pointer color-picker bg-white p-0.5" required
                                                       title="Warna opsi">
                                                <input type="hidden" class="color-text-input" value="#b91c1c">
                                            </div>
                                            <button type="button" onclick="removeOption(this)"
                                                    class="mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center text-news-muted hover:text-red-600 transition-colors"
                                                    title="Hapus opsi"
                                                    {{ $i < 2 ? 'style="display:none;"' : '' }}>
                                                <i class="fas fa-trash text-xs"></i>
                                            </button>
                                        </div>
                                    </div>
                                @endfor
                            @endif
                        </div>
                    </div>

                    @error('options')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    @error('options.*')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </section>

                {{-- Pengaturan --}}
                <section>
                    <div class="flex items-center gap-2 mb-4 pb-2 border-b border-news-line">
                        <span class="inline-flex h-6 w-6 items-center justify-center bg-news-ink text-white text-[10px] font-bold">4</span>
                        <h4 class="text-sm font-bold uppercase tracking-wider text-news-ink">Pengaturan</h4>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label for="is_active" class="flex items-start gap-3 border border-news-line p-3 cursor-pointer hover:border-news-ink transition-colors">
                            <input type="checkbox" name="is_active" id="is_active" value="1"
                                   {{ old('is_active', true) ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            <span>
                                <span class="block text-sm font-medium text-news-ink">Polling Aktif</span>
                                <span class="block text-xs text-news-muted mt-0.5">Ditampilkan di widget jika periode valid</span>
                            </span>
                        </label>

                        <label for="allow_anonymous" class="flex items-start gap-3 border border-news-line p-3 cursor-pointer hover:border-news-ink transition-colors">
                            <input type="checkbox" name="allow_anonymous" id="allow_anonymous" value="1"
                                   {{ old('allow_anonymous', false) ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            <span>
                                <span class="block text-sm font-medium text-news-ink">Voting Anonim</span>
                                <span class="block text-xs text-news-muted mt-0.5">Izinkan vote tanpa login (batas per IP)</span>
                            </span>
                        </label>

                        <label for="show_results" class="flex items-start gap-3 border border-news-line p-3 cursor-pointer hover:border-news-ink transition-colors">
                            <input type="checkbox" name="show_results" id="show_results" value="1"
                                   {{ old('show_results', true) ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            <span>
                                <span class="block text-sm font-medium text-news-ink">Tampilkan Hasil</span>
                                <span class="block text-xs text-news-muted mt-0.5">User bisa melihat persentase suara</span>
                            </span>
                        </label>

                        <label for="show_vote_count" class="flex items-start gap-3 border border-news-line p-3 cursor-pointer hover:border-news-ink transition-colors">
                            <input type="checkbox" name="show_vote_count" id="show_vote_count" value="1"
                                   {{ old('show_vote_count', true) ? 'checked' : '' }}
                                   class="mt-0.5 h-4 w-4 text-news-accent focus:ring-news-accent border-news-line rounded">
                            <span>
                                <span class="block text-sm font-medium text-news-ink">Tampilkan Jumlah Suara</span>
                                <span class="block text-xs text-news-muted mt-0.5">Angka total suara per opsi</span>
                            </span>
                        </label>
                    </div>
                </section>
            </div>

            <div class="px-6 py-4 bg-news-paper border-t border-news-line flex flex-wrap justify-end gap-3">
                <a href="{{ route('admin.polls.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary">
                    <i class="fas fa-save mr-2"></i>Simpan Polling
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
    optionItem.className = 'option-item p-3';

    const optionNumber = container.children.length + 1;
    const colors = ['#b91c1c', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6', '#EC4899', '#06B6D4', '#84CC16'];
    const color = colors[optionIndex % colors.length];

    optionItem.innerHTML = `
        <div class="flex items-start gap-2">
            <span class="option-badge mt-1 inline-flex h-8 w-8 shrink-0 items-center justify-center bg-news-ink text-white text-xs font-bold">${optionNumber}</span>
            <div class="min-w-0 flex-1 space-y-2">
                <h4 class="sr-only">Opsi ${optionNumber}</h4>
                <input type="text" name="options[${optionIndex}][text]" value=""
                       class="form-input" placeholder="Teks opsi *" required>
                <input type="text" name="options[${optionIndex}][description]" value=""
                       class="form-input text-xs" placeholder="Deskripsi (opsional)">
            </div>
            <div class="shrink-0 mt-1">
                <input type="color" name="options[${optionIndex}][color]" value="${color}"
                       class="h-10 w-12 border border-news-line cursor-pointer color-picker bg-white p-0.5" required
                       title="Warna opsi">
                <input type="hidden" class="color-text-input" value="${color}">
            </div>
            <button type="button" onclick="removeOption(this)"
                    class="mt-1 inline-flex h-10 w-10 shrink-0 items-center justify-center text-news-muted hover:text-red-600 transition-colors"
                    title="Hapus opsi">
                <i class="fas fa-trash text-xs"></i>
            </button>
        </div>
    `;

    container.appendChild(optionItem);
    optionIndex++;

    updateOptionNumbers();
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

        const badge = item.querySelector('.option-badge');
        if (badge) {
            badge.textContent = String(index + 1);
        }

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

document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('options-container');
    if (container) {
        setupColorPickerSync(container);
    }

    document.getElementById('poll-form').addEventListener('submit', function(e) {
        const container = document.getElementById('options-container');
        const optionItems = container.querySelectorAll('.option-item');

        if (optionItems.length < 2) {
            e.preventDefault();
            alert('Minimal 2 opsi diperlukan');
            return false;
        }

        let isValid = true;
        optionItems.forEach((item) => {
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

        if (pollTypeSelect.value === 'single') {
            maxVotesInput.value = 1;
            maxVotesInput.setAttribute('readonly', 'readonly');
        }
    }

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
