@php
    $pollService = new \App\Services\PollService();
    $pollData = $pollService->getActivePoll();
@endphp

@if($pollData && $pollData['poll'])
    @php $poll = $pollData['poll']; @endphp
    <div class="border border-news-line poll-widget" data-poll-id="{{ $poll->id }}">
        <div class="bg-news-ink text-white px-4 py-2.5 flex items-center justify-between gap-2">
            <h2 class="text-xs font-bold uppercase tracking-[0.15em]">Polling</h2>
            <span class="text-[10px] font-bold uppercase tracking-wider text-white/60 shrink-0">
                {{ $poll->status_label }}
            </span>
        </div>

        <div class="p-4">
            <h3 class="text-sm font-bold text-news-ink leading-snug">{{ $poll->title }}</h3>
            @if($poll->description)
                <p class="mt-1 text-[12px] text-news-muted">{{ $poll->description }}</p>
            @endif

            <div class="mt-4 space-y-2 poll-options">
                @if($poll->poll_type === 'single')
                    @foreach($poll->options as $option)
                        <label class="poll-option-label flex items-center gap-2 px-3 py-2 border border-news-line cursor-pointer hover:border-news-ink transition-colors">
                            <input type="radio"
                                   name="poll_option"
                                   value="{{ $option->id }}"
                                   class="poll-option-input text-news-accent focus:ring-news-accent">
                            <span class="poll-option-text text-sm text-news-ink">{{ $option->option_text }}</span>
                        </label>
                    @endforeach
                @else
                    @foreach($poll->options as $option)
                        <label class="poll-option-label flex items-center gap-2 px-3 py-2 border border-news-line cursor-pointer hover:border-news-ink transition-colors">
                            <input type="checkbox"
                                   name="poll_option[]"
                                   value="{{ $option->id }}"
                                   class="poll-option-input text-news-accent focus:ring-news-accent">
                            <span class="poll-option-text text-sm text-news-ink">{{ $option->option_text }}</span>
                        </label>
                    @endforeach
                @endif
            </div>

            <div class="mt-4 flex flex-wrap gap-2">
                <button type="button"
                        class="poll-submit-btn px-3 py-1.5 bg-news-ink text-white text-xs font-semibold hover:bg-news-accent transition-colors disabled:opacity-40 disabled:cursor-not-allowed"
                        disabled>
                    Kirim Suara
                </button>
                @if($poll->show_results)
                    <button type="button"
                            class="poll-results-btn px-3 py-1.5 border border-news-line text-news-ink text-xs font-semibold hover:border-news-ink transition-colors">
                        Lihat Hasil
                    </button>
                @endif
            </div>

            <div class="mt-3 pt-3 border-t border-news-line flex flex-wrap items-center justify-between gap-2 text-[11px] text-news-muted">
                <span>{{ $poll->total_votes }} suara</span>
                @if($poll->end_date)
                    <span>Berakhir {{ $poll->formatted_end_date }}</span>
                @endif
            </div>

            <div class="poll-results mt-4 pt-3 border-t border-news-line" style="display: none;">
                <h4 class="text-[10px] font-bold uppercase tracking-wider text-news-muted mb-3">Hasil Polling</h4>
                <div class="space-y-3">
                    @foreach($poll->options as $option)
                        <div>
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <span class="text-xs text-news-ink">{{ $option->option_text }}</span>
                                <span class="text-xs font-bold text-news-accent tabular-nums">{{ $option->vote_percentage }}%</span>
                            </div>
                            <div class="h-1.5 bg-news-line overflow-hidden">
                                <div class="h-full bg-news-accent transition-all duration-300"
                                     style="width: {{ $option->vote_percentage }}%;"></div>
                            </div>
                            <div class="mt-0.5 text-right text-[10px] text-news-muted">{{ $option->vote_count }} suara</div>
                        </div>
                    @endforeach
                </div>
            </div>

            @if(!empty($pollData['updated_at']))
                <p class="mt-3 text-center text-[10px] text-news-muted">Update {{ $pollData['updated_at'] }}</p>
            @endif
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pollWidget = document.querySelector('.poll-widget');
        if (!pollWidget) return;

        const pollId = pollWidget.dataset.pollId;
        const submitBtn = pollWidget.querySelector('.poll-submit-btn');
        const resultsBtn = pollWidget.querySelector('.poll-results-btn');
        const resultsDiv = pollWidget.querySelector('.poll-results');
        const optionInputs = pollWidget.querySelectorAll('.poll-option-input');

        optionInputs.forEach(input => {
            input.addEventListener('change', function() {
                const hasSelection = Array.from(optionInputs).some(input => input.checked);
                submitBtn.disabled = !hasSelection;
            });
        });

        submitBtn.addEventListener('click', function() {
            const selectedOptions = Array.from(optionInputs)
                .filter(input => input.checked)
                .map(input => input.value);

            if (selectedOptions.length === 0) {
                alert('Pilih minimal satu pilihan');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.textContent = 'Mengirim...';

            fetch('/api/widgets/submit-poll-vote', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    poll_id: pollId,
                    option_ids: selectedOptions
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert(data.message);
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Kirim Suara';
                }
            })
            .catch(() => {
                alert('Terjadi kesalahan saat mengirim suara');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Kirim Suara';
            });
        });

        if (resultsBtn) {
            resultsBtn.addEventListener('click', function() {
                if (resultsDiv.style.display === 'none') {
                    resultsDiv.style.display = 'block';
                    resultsBtn.textContent = 'Sembunyikan Hasil';
                } else {
                    resultsDiv.style.display = 'none';
                    resultsBtn.textContent = 'Lihat Hasil';
                }
            });
        }
    });
    </script>
@endif
