@extends('layouts.admin-simple')

@section('title', 'Detail Polling')
@section('page-title', 'Detail Polling')
@section('page-subtitle', $poll->title)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.polls.index') }}" class="btn-secondary"><i class="fas fa-arrow-left mr-2"></i>Kembali</a>
        <div class="flex gap-2">
            <a href="{{ route('admin.polls.edit', $poll) }}" class="btn-primary"><i class="fas fa-edit mr-2"></i>Edit</a>
            <form action="{{ route('admin.polls.reset-votes', $poll) }}" method="POST" onsubmit="return window.pesbarConfirmForm(event, 'Reset semua suara polling ini?', {danger:true})">
                @csrf
                <button type="submit" class="btn-secondary text-amber-700">Reset Votes</button>
            </form>
        </div>
    </div>

    <div class="bg-white border border-news-line p-6">
        <div class="flex flex-wrap gap-2 mb-4">
            <span class="px-2.5 py-0.5 text-xs font-semibold {{ $poll->is_active ? 'bg-emerald-50 text-emerald-800' : 'bg-red-50 text-news-accent' }}">
                {{ $poll->is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
            <span class="px-2.5 py-0.5 text-xs font-semibold bg-news-paper text-news-ink">{{ $poll->poll_type }}</span>
            <span class="px-2.5 py-0.5 text-xs font-semibold bg-news-paper text-news-muted">Total suara: {{ $poll->total_votes ?? collect($results)->sum('vote_count') }}</span>
        </div>
        @if($poll->description)
            <p class="text-news-muted mb-6">{{ $poll->description }}</p>
        @endif

        <h3 class="text-sm font-bold uppercase tracking-wider text-news-muted mb-3">Hasil</h3>
        <div class="space-y-3">
            @forelse($results as $row)
                <div>
                    <div class="flex justify-between text-sm mb-1">
                        <span class="font-medium text-news-ink">{{ $row['option']->option_text }}</span>
                        <span class="text-news-muted">{{ $row['vote_count'] }} ({{ $row['percentage'] }}%)</span>
                    </div>
                    <div class="h-2 bg-news-paper rounded overflow-hidden">
                        <div class="h-full" style="width: {{ $row['percentage'] }}%; background: {{ $row['option']->color ?? '#0a0a0a' }}"></div>
                    </div>
                </div>
            @empty
                <p class="text-sm text-news-muted">Belum ada opsi / suara.</p>
            @endforelse
        </div>
    </div>
</div>
@endsection
