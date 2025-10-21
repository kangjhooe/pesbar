@php
    $pollService = new \App\Services\PollService();
    $pollData = $pollService->getActivePoll();
@endphp

@if($pollData && $pollData['poll'])
    @php $poll = $pollData['poll']; @endphp
    <div class="widget poll-widget" data-poll-id="{{ $poll->id }}">
        <div class="widget-header">
            <h4 class="widget-title">
                <i class="fas fa-poll"></i>
                Polling
            </h4>
            <span class="poll-status badge bg-{{ $poll->status_color }}-100 text-{{ $poll->status_color }}-600">
                {{ $poll->status_label }}
            </span>
        </div>
        
        <div class="widget-content">
            <div class="poll-question">
                <h5 class="poll-title">{{ $poll->title }}</h5>
                @if($poll->description)
                    <p class="poll-description">{{ $poll->description }}</p>
                @endif
            </div>
            
            <div class="poll-options">
                @if($poll->poll_type === 'single')
                    <!-- Single Choice Poll -->
                    @foreach($poll->options as $option)
                        <div class="poll-option">
                            <label class="poll-option-label">
                                <input type="radio" 
                                       name="poll_option" 
                                       value="{{ $option->id }}" 
                                       class="poll-option-input">
                                <span class="poll-option-text">{{ $option->option_text }}</span>
                            </label>
                        </div>
                    @endforeach
                @else
                    <!-- Multiple Choice Poll -->
                    @foreach($poll->options as $option)
                        <div class="poll-option">
                            <label class="poll-option-label">
                                <input type="checkbox" 
                                       name="poll_option[]" 
                                       value="{{ $option->id }}" 
                                       class="poll-option-input">
                                <span class="poll-option-text">{{ $option->option_text }}</span>
                            </label>
                        </div>
                    @endforeach
                @endif
            </div>
            
            <div class="poll-actions">
                <button type="button" class="btn btn-primary btn-sm poll-submit-btn" disabled>
                    <i class="fas fa-vote-yea mr-1"></i>
                    Kirim Suara
                </button>
                @if($poll->show_results)
                    <button type="button" class="btn btn-outline-secondary btn-sm poll-results-btn">
                        <i class="fas fa-chart-bar mr-1"></i>
                        Lihat Hasil
                    </button>
                @endif
            </div>
            
            <div class="poll-info">
                <div class="poll-meta">
                    <span class="poll-vote-count">
                        <i class="fas fa-users mr-1"></i>
                        {{ $poll->total_votes }} suara
                    </span>
                    @if($poll->end_date)
                        <span class="poll-end-date">
                            <i class="fas fa-clock mr-1"></i>
                            Berakhir: {{ $poll->formatted_end_date }}
                        </span>
                    @endif
                </div>
            </div>
            
            <!-- Poll Results (Hidden by default) -->
            <div class="poll-results" style="display: none;">
                <h6 class="results-title">Hasil Polling</h6>
                <div class="results-list">
                    @foreach($poll->options as $option)
                        <div class="result-item">
                            <div class="result-option">
                                <span class="result-text">{{ $option->option_text }}</span>
                                <span class="result-percentage">{{ $option->vote_percentage }}%</span>
                            </div>
                            <div class="result-bar">
                                <div class="result-progress" 
                                     style="width: {{ $option->vote_percentage }}%; background-color: {{ $option->color }};"></div>
                            </div>
                            <div class="result-votes">{{ $option->vote_count }} suara</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        
        <div class="widget-footer">
            @if(isset($pollData['updated_at']))
            <div class="text-center">
                <small class="text-muted">
                    <i class="fas fa-sync-alt mr-1"></i>
                    Update: {{ $pollData['updated_at'] }}
                </small>
            </div>
            @endif
        </div>
    </div>

    <style>
    .poll-widget {
        background: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        overflow: hidden;
    }

    .poll-widget .widget-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 12px 16px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .poll-widget .widget-title {
        margin: 0;
        font-size: 14px;
        font-weight: 600;
    }

    .poll-widget .widget-title i {
        margin-right: 6px;
        font-size: 12px;
    }

    .poll-widget .widget-content {
        padding: 16px;
    }

    .poll-question {
        margin-bottom: 16px;
    }

    .poll-title {
        font-size: 14px;
        font-weight: 600;
        color: #333;
        margin-bottom: 6px;
    }

    .poll-description {
        font-size: 12px;
        color: #666;
        margin: 0;
    }

    .poll-options {
        margin-bottom: 16px;
    }

    .poll-option {
        margin-bottom: 10px;
    }

    .poll-option-label {
        display: flex;
        align-items: center;
        cursor: pointer;
        padding: 8px;
        border: 2px solid #e9ecef;
        border-radius: 6px;
        transition: all 0.2s ease;
    }

    .poll-option-label:hover {
        border-color: #667eea;
        background-color: #f8f9ff;
    }

    .poll-option-input {
        margin-right: 8px;
        transform: scale(1.1);
    }

    .poll-option-input:checked + .poll-option-text {
        color: #667eea;
        font-weight: 600;
    }

    .poll-option-label:has(.poll-option-input:checked) {
        border-color: #667eea;
        background-color: #f0f4ff;
    }

    .poll-option-text {
        flex: 1;
        font-size: 12px;
        color: #333;
    }

    .poll-actions {
        display: flex;
        gap: 8px;
        margin-bottom: 12px;
    }

    .poll-actions .btn {
        font-size: 11px;
        padding: 6px 12px;
    }

    .poll-info {
        border-top: 1px solid #e9ecef;
        padding-top: 12px;
    }

    .poll-meta {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 11px;
        color: #666;
    }

    .poll-results {
        border-top: 1px solid #e9ecef;
        padding-top: 12px;
        margin-top: 12px;
    }

    .results-title {
        font-size: 13px;
        font-weight: 600;
        color: #333;
        margin-bottom: 12px;
    }

    .result-item {
        margin-bottom: 12px;
    }

    .result-option {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .result-text {
        font-size: 12px;
        color: #333;
    }

    .result-percentage {
        font-size: 11px;
        font-weight: 600;
        color: #667eea;
    }

    .result-bar {
        height: 6px;
        background-color: #e9ecef;
        border-radius: 3px;
        overflow: hidden;
        margin-bottom: 2px;
    }

    .result-progress {
        height: 100%;
        transition: width 0.3s ease;
    }

    .result-votes {
        font-size: 10px;
        color: #666;
        text-align: right;
    }

    .widget-footer {
        padding: 8px 16px;
        background-color: #f8f9fa;
        border-top: 1px solid #e9ecef;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .poll-widget .widget-header {
            padding: 12px 15px;
            flex-direction: column;
            align-items: flex-start;
            gap: 8px;
        }
        
        .poll-widget .widget-content {
            padding: 15px;
        }
        
        .poll-actions {
            flex-direction: column;
        }
        
        .poll-meta {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
        }
    }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const pollWidget = document.querySelector('.poll-widget');
        if (!pollWidget) return;

        const pollId = pollWidget.dataset.pollId;
        const submitBtn = pollWidget.querySelector('.poll-submit-btn');
        const resultsBtn = pollWidget.querySelector('.poll-results-btn');
        const resultsDiv = pollWidget.querySelector('.poll-results');
        const optionInputs = pollWidget.querySelectorAll('.poll-option-input');

        // Enable submit button when option is selected
        optionInputs.forEach(input => {
            input.addEventListener('change', function() {
                const hasSelection = Array.from(optionInputs).some(input => input.checked);
                submitBtn.disabled = !hasSelection;
            });
        });

        // Submit vote
        submitBtn.addEventListener('click', function() {
            const selectedOptions = Array.from(optionInputs)
                .filter(input => input.checked)
                .map(input => input.value);

            if (selectedOptions.length === 0) {
                alert('Pilih minimal satu pilihan');
                return;
            }

            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>Mengirim...';

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
                    // Reload the widget to show updated results
                    location.reload();
                } else {
                    alert(data.message);
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-vote-yea mr-1"></i>Kirim Suara';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Terjadi kesalahan saat mengirim suara');
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-vote-yea mr-1"></i>Kirim Suara';
            });
        });

        // Show/hide results
        if (resultsBtn) {
            resultsBtn.addEventListener('click', function() {
                if (resultsDiv.style.display === 'none') {
                    resultsDiv.style.display = 'block';
                    resultsBtn.innerHTML = '<i class="fas fa-eye-slash mr-1"></i>Sembunyikan Hasil';
                } else {
                    resultsDiv.style.display = 'none';
                    resultsBtn.innerHTML = '<i class="fas fa-chart-bar mr-1"></i>Lihat Hasil';
                }
            });
        }
    });
    </script>
@endif
