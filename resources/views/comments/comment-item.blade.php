@php
    $isOwner = auth()->check() && $comment->user_id === auth()->id();
    $isLiked = $comment->isLikedByUser();
    $isDisliked = $comment->isDislikedByUser();
@endphp

<div class="comment-item {{ $level > 0 ? 'ml-8 mt-4' : '' }} mb-4 bg-white border border-news-line overflow-hidden" 
     data-comment-id="{{ $comment->id }}" 
     id="comment-{{ $comment->id }}">
    <div class="p-4 md:p-5">
        <div class="flex items-start gap-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                @if($comment->user && $comment->user->profile && $comment->user->profile->avatar)
                    <img src="{{ asset('storage/' . $comment->user->profile->avatar) }}" 
                         alt="{{ $comment->name }}" 
                         class="w-12 h-12 object-cover border border-news-line">
                @else
                    <div class="w-12 h-12 bg-news-ink flex items-center justify-center text-white font-bold text-lg">
                        {{ strtoupper(substr($comment->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <!-- Comment Content -->
            <div class="flex-1 min-w-0">
                <!-- Header -->
                <div class="flex items-start justify-between mb-3 gap-3">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 flex-wrap mb-1">
                            <h5 class="font-bold text-news-ink text-base">{{ $comment->name }}</h5>
                            @if($comment->user)
                                <x-user-role-badge :user="$comment->user" size="xs" />
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-wrap text-xs text-news-muted">
                            @if($comment->user_id && $comment->user && $comment->user->username)
                                <a href="{{ route('penulis.public-profile', $comment->user->username) }}" 
                                   class="text-news-accent hover:text-news-ink font-medium flex items-center gap-1 transition-colors hover:underline">
                                    <i class="fas fa-at text-xs"></i>
                                    <span>{{ $comment->user->username }}</span>
                                </a>
                            @endif
                            <span class="flex items-center gap-1">
                                <i class="far fa-clock"></i>
                                <span>{{ $comment->created_at->diffForHumans() }}</span>
                            </span>
                        </div>
                    </div>
                    
                    @if($isOwner)
                    <div class="flex items-center gap-1">
                        <button onclick="editComment({{ $comment->id }}, {!! json_encode($comment->comment, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!})" 
                                class="p-2 text-news-muted hover:text-news-ink transition-colors" 
                                title="Edit Komentar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment) }}" 
                              method="POST" 
                              class="inline" 
                              onsubmit="return window.pesbarConfirmForm(event, 'Apakah Anda yakin ingin menghapus komentar ini?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200" 
                                    title="Hapus Komentar">
                                <i class="fas fa-trash text-sm"></i>
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                
                <!-- Comment Text -->
                <div class="comment-text mb-4">
                    <p class="text-news-ink leading-relaxed whitespace-pre-wrap text-[15px]">{{ $comment->comment }}</p>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-3 pt-3 border-t border-news-line">
                    <!-- Like/Dislike -->
                    <div class="flex items-center gap-2">
                        <button onclick="toggleLike({{ $comment->id }}, true)" 
                                class="flex items-center gap-2 px-3 py-1.5 transition-colors {{ $isLiked ? 'bg-green-50 text-green-700 border border-green-300' : 'bg-white text-news-muted hover:text-green-700 border border-news-line hover:border-green-300' }}"
                                id="like-btn-{{ $comment->id }}"
                                title="Suka">
                            <i class="fas fa-thumbs-up text-sm"></i>
                            <span class="text-sm font-medium" id="likes-count-{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                        </button>
                        <button onclick="toggleLike({{ $comment->id }}, false)" 
                                class="flex items-center gap-2 px-3 py-1.5 transition-colors {{ $isDisliked ? 'bg-red-50 text-red-700 border border-red-300' : 'bg-white text-news-muted hover:text-red-700 border border-news-line hover:border-red-300' }}"
                                id="dislike-btn-{{ $comment->id }}"
                                title="Tidak suka">
                            <i class="fas fa-thumbs-down text-sm"></i>
                            <span class="text-sm font-medium" id="dislikes-count-{{ $comment->id }}">{{ $comment->dislikes_count ?? 0 }}</span>
                        </button>
                    </div>
                    
                    <!-- Reply Button -->
                    @auth
                    <button onclick="replyToComment({{ $comment->id }}, {!! json_encode($comment->name, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_AMP | JSON_HEX_QUOT) !!})" 
                            class="flex items-center gap-2 px-3 py-1.5 text-news-accent hover:text-news-ink font-medium transition-colors border border-transparent hover:border-news-line">
                        <i class="fas fa-reply text-sm"></i>
                        <span class="text-sm">Balas</span>
                    </button>
                    @else
                    <a href="{{ route('login') }}" 
                       class="flex items-center gap-2 px-3 py-1.5 text-news-accent hover:text-news-ink font-medium transition-colors border border-transparent hover:border-news-line">
                        <i class="fas fa-reply text-sm"></i>
                        <span class="text-sm">Balas</span>
                    </a>
                    @endauth
                </div>
            </div>
            
            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="mt-5 pt-4 border-t border-news-line replies-container">
                    <div class="space-y-3">
                        @foreach($comment->replies as $reply)
                            @include('comments.comment-item', ['comment' => $reply, 'level' => $level + 1])
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div id="edit-comment-modal-{{ $comment->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white border border-news-line max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-display text-xl font-bold text-news-ink">
                    Edit Komentar
                </h3>
                <button onclick="closeEditModal({{ $comment->id }})" class="text-news-muted hover:text-news-ink">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            
            <form id="edit-comment-form-{{ $comment->id }}" 
                  action="{{ route('comments.update', $comment) }}" 
                  method="POST" 
                  class="space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm font-medium text-news-ink mb-2">Komentar</label>
                    <textarea name="comment" 
                              id="edit-comment-text-{{ $comment->id }}"
                              rows="5" 
                              required
                              maxlength="1000"
                              class="w-full px-3 py-2 border border-news-line focus:outline-none focus:ring-2 focus:ring-news-accent focus:border-transparent">{{ $comment->comment }}</textarea>
                    <div class="flex justify-end mt-1">
                        <span class="text-[11px] text-news-muted">
                            <span id="edit-char-count-{{ $comment->id }}">{{ strlen($comment->comment) }}</span>/1000 karakter
                        </span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeEditModal({{ $comment->id }})"
                            class="px-5 py-2 border border-news-line text-news-ink hover:border-news-ink transition-colors text-sm font-medium">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-5 py-2 bg-news-ink text-white hover:bg-news-accent transition-colors text-sm font-semibold">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

