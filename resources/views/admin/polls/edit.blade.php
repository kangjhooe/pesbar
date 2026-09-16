@extends('layouts.admin-simple')

@section('title', 'Edit Polling')

@section('content')
<div class="p-6">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-news-ink">Edit Polling</h1>
            <p class="text-news-muted mt-1">{{ $poll->title }}</p>
        </div>
        <a href="{{ route('admin.polls.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
        </a>
    </div>

    <div class="bg-white border border-news-line">
        <form action="{{ route('admin.polls.update', $poll) }}" method="POST" id="poll-form">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-6">
                <div>
                    <h3 class="text-lg font-medium text-news-ink mb-4">Informasi Dasar</h3>
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-news-ink mb-2">Judul Polling *</label>
                            <input type="text" name="title" value="{{ old('title', $poll->title) }}" class="form-input" required>
                            @error('title')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>
                        <div class="lg:col-span-2">
                            <label class="block text-sm font-medium text-news-ink mb-2">Deskripsi</label>
                            <textarea name="description" rows="3" class="form-input">{{ old('description', $poll->description) }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-2">Tipe Polling *</label>
                            <select name="poll_type" id="poll_type" class="form-select" required>
                                <option value="single" {{ old('poll_type', $poll->poll_type) == 'single' ? 'selected' : '' }}>Pilihan Tunggal</option>
                                <option value="multiple" {{ old('poll_type', $poll->poll_type) == 'multiple' ? 'selected' : '' }}>Pilihan Ganda</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-2">Maksimal Pilihan per User *</label>
                            <input type="number" name="max_votes_per_user" id="max_votes_per_user"
                                   value="{{ old('max_votes_per_user', $poll->max_votes_per_user) }}"
                                   class="form-input" min="1" max="10" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-2">Tanggal Mulai</label>
                            <input type="datetime-local" name="start_date" class="form-input"
                                   value="{{ old('start_date', $poll->start_date?->format('Y-m-d\TH:i')) }}">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-news-ink mb-2">Tanggal Selesai</label>
                            <input type="datetime-local" name="end_date" class="form-input"
                                   value="{{ old('end_date', $poll->end_date?->format('Y-m-d\TH:i')) }}">
                        </div>
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-lg font-medium text-news-ink">Opsi Polling *</h3>
                        <button type="button" onclick="addOption()" class="btn-sm btn-primary"><i class="fas fa-plus mr-2"></i>Tambah Opsi</button>
                    </div>
                    <div id="options-container" class="space-y-4">
                        @php
                            $options = old('options');
                            if (!$options) {
                                $options = $poll->options->map(fn ($o) => [
                                    'id' => $o->id,
                                    'text' => $o->option_text,
                                    'color' => $o->color ?: '#b91c1c',
                                    'description' => $o->description,
                                ])->values()->all();
                            }
                        @endphp
                        @foreach($options as $index => $option)
                            <div class="option-item border border-news-line rounded-lg p-4">
                                <div class="flex items-start justify-between mb-3">
                                    <h4 class="text-sm font-medium text-news-ink">Opsi {{ $index + 1 }}</h4>
                                    <button type="button" onclick="removeOption(this)" class="text-red-600 hover:text-red-800" @if(count($options) <= 2) style="display:none" @endif>
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                @if(!empty($option['id']))
                                    <input type="hidden" name="options[{{ $index }}][id]" value="{{ $option['id'] }}">
                                @endif
                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-news-ink mb-2">Teks Opsi *</label>
                                        <input type="text" name="options[{{ $index }}][text]" value="{{ $option['text'] ?? '' }}" class="form-input" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-news-ink mb-2">Warna *</label>
                                        <input type="color" name="options[{{ $index }}][color]" value="{{ $option['color'] ?? '#b91c1c' }}" class="h-10 w-20 rounded border border-news-line" required>
                                    </div>
                                    <div class="lg:col-span-2">
                                        <label class="block text-sm font-medium text-news-ink mb-2">Deskripsi</label>
                                        <textarea name="options[{{ $index }}][description]" rows="2" class="form-input">{{ $option['description'] ?? '' }}</textarea>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @error('options')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <div>
                    <h3 class="text-lg font-medium text-news-ink mb-4">Pengaturan</h3>
                    <div class="space-y-3">
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="is_active" value="1" {{ old('is_active', $poll->is_active) ? 'checked' : '' }} class="rounded border-news-line"> Polling Aktif</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="allow_anonymous" value="1" {{ old('allow_anonymous', $poll->allow_anonymous) ? 'checked' : '' }} class="rounded border-news-line"> Izinkan Voting Anonim</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="show_results" value="1" {{ old('show_results', $poll->show_results) ? 'checked' : '' }} class="rounded border-news-line"> Tampilkan Hasil</label>
                        <label class="flex items-center gap-2 text-sm"><input type="checkbox" name="show_vote_count" value="1" {{ old('show_vote_count', $poll->show_vote_count) ? 'checked' : '' }} class="rounded border-news-line"> Tampilkan Jumlah Suara</label>
                    </div>
                </div>
            </div>

            <div class="px-6 py-4 bg-news-paper border-t border-news-line flex justify-end gap-3">
                <a href="{{ route('admin.polls.index') }}" class="btn-secondary">Batal</a>
                <button type="submit" class="btn-primary"><i class="fas fa-save mr-2"></i>Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
let optionIndex = {{ count($options) }};
function addOption() {
    const container = document.getElementById('options-container');
    const idx = optionIndex++;
    const el = document.createElement('div');
    el.className = 'option-item border border-news-line rounded-lg p-4';
    el.innerHTML = `
        <div class="flex items-start justify-between mb-3">
            <h4 class="text-sm font-medium text-news-ink">Opsi baru</h4>
            <button type="button" onclick="removeOption(this)" class="text-red-600"><i class="fas fa-trash"></i></button>
        </div>
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
            <div class="lg:col-span-2"><label class="block text-sm font-medium mb-2">Teks Opsi *</label>
            <input type="text" name="options[${idx}][text]" class="form-input" required></div>
            <div><label class="block text-sm font-medium mb-2">Warna *</label>
            <input type="color" name="options[${idx}][color]" value="#b91c1c" class="h-10 w-20 rounded border" required></div>
            <div class="lg:col-span-2"><label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea name="options[${idx}][description]" rows="2" class="form-input"></textarea></div>
        </div>`;
    container.appendChild(el);
}
function removeOption(btn) {
    const container = document.getElementById('options-container');
    if (container.children.length <= 2) { alert('Minimal 2 opsi'); return; }
    btn.closest('.option-item').remove();
}
</script>
@endsection
