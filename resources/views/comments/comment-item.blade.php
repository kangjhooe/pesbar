@php
    $isOwner = auth()->check() && $comment->user_id === auth()->id();
    $isLiked = $comment->isLikedByUser();
    $isDisliked = $comment->isDislikedByUser();
@endphp

<div class="comment-item border-l-4 {{ $level > 0 ? 'border-gray-300 ml-6' : 'border-primary-500' }} pl-4 py-3 bg-gray-50 rounded-r-lg hover:bg-gray-100 transition-colors" 
     data-comment-id="{{ $comment->id }}" 
     id="comment-{{ $comment->id }}">
    <div class="flex items-start space-x-3">
        <!-- Avatar -->
        <div class="flex-shrink-0">
            @if($comment->user && $comment->user->profile_photo)
                <img src="{{ asset('storage/' . $comment->user->profile_photo) }}" 
                     alt="{{ $comment->name }}" 
                     class="w-10 h-10 rounded-full object-cover border-2 border-primary-200">
            @else
                <div class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-semibold shadow-md">
                    {{ strtoupper(substr($comment->name, 0, 1)) }}
                </div>
            @endif
        </div>
        
        <!-- Comment Content -->
        <div class="flex-1 min-w-0">
            <!-- Header -->
            <div class="flex items-center justify-between mb-2 flex-wrap gap-2">
                <div class="flex items-center gap-2 flex-wrap">
                    <h5 class="font-semibold text-gray-900">{{ $comment->name }}</h5>
                    @if($comment->user_id && $comment->user && $comment->user->username)
                        <a href="{{ route('penulis.public-profile', $comment->user->username) }}" 
                           class="text-xs text-primary-600 hover:text-primary-800 flex items-center gap-1 font-medium">
                            <i class="fas fa-check-circle text-green-500" title="User Terverifikasi"></i>
                            <span>@{{ $comment->user->username }}</span>
                        </a>
                    @endif
                    <span class="text-xs text-gray-500">
                        <i class="far fa-clock mr-1"></i>
                        {{ $comment->created_at->diffForHumans() }}
                    </span>
                </div>
                
                @if($isOwner)
                <div class="flex items-center gap-2">
                    <button onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')" 
                            class="text-xs text-blue-600 hover:text-blue-800 p-1 rounded transition-colors" 
                            title="Edit Komentar">
                        <i class="fas fa-edit"></i>
                    </button>
                    <form action="{{ route('comments.destroy', $comment) }}" 
                          method="POST" 
                          class="inline" 
                          onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="text-xs text-red-600 hover:text-red-800 p-1 rounded transition-colors" 
                                title="Hapus Komentar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
                @endif
            </div>
            
            <!-- Comment Text -->
            <div class="comment-text mb-3">
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ $comment->comment }}</p>
            </div>
            
            <!-- Actions -->
            <div class="flex items-center gap-4 text-sm">
                <!-- Like/Dislike -->
                <div class="flex items-center gap-2">
                    <button onclick="toggleLike({{ $comment->id }}, true)" 
                            class="flex items-center gap-1 px-3 py-1 rounded-full transition-all {{ $isLiked ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600 hover:bg-green-50' }}"
                            id="like-btn-{{ $comment->id }}"
                            title="Suka">
                        <i class="fas fa-thumbs-up"></i>
                        <span id="likes-count-{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                    </button>
                    <button onclick="toggleLike({{ $comment->id }}, false)" 
                            class="flex items-center gap-1 px-3 py-1 rounded-full transition-all {{ $isDisliked ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-600 hover:bg-red-50' }}"
                            id="dislike-btn-{{ $comment->id }}"
                            title="Tidak suka">
                        <i class="fas fa-thumbs-down"></i>
                        <span id="dislikes-count-{{ $comment->id }}">{{ $comment->dislikes_count ?? 0 }}</span>
                    </button>
                </div>
                
                <!-- Reply Button -->
                @auth
                <button onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->name) }}')" 
                        class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1 transition-colors">
                    <i class="fas fa-reply"></i>
                    <span>Balas</span>
                </button>
                @else
                <a href="{{ route('login') }}" 
                   class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1 transition-colors">
                    <i class="fas fa-reply"></i>
                    <span>Balas</span>
                </a>
                @endauth
            </div>
            
            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="mt-4 space-y-3 replies-container">
                    @foreach($comment->replies as $reply)
                        @include('comments.comment-item', ['comment' => $reply, 'level' => $level + 1])
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Edit Comment Modal -->
<div id="edit-comment-modal-{{ $comment->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-xl font-bold text-gray-900">
                    <i class="fas fa-edit mr-2 text-primary-600"></i>Edit Komentar
                </h3>
                <button onclick="closeEditModal({{ $comment->id }})" class="text-gray-400 hover:text-gray-600">
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
                    <label class="block text-sm font-medium text-gray-700 mb-2">Komentar</label>
                    <textarea name="comment" 
                              id="edit-comment-text-{{ $comment->id }}"
                              rows="5" 
                              required
                              maxlength="1000"
                              class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent">{{ $comment->comment }}</textarea>
                    <div class="flex justify-end mt-1">
                        <span class="text-xs text-gray-500">
                            <span id="edit-char-count-{{ $comment->id }}">{{ strlen($comment->comment) }}</span>/1000 karakter
                        </span>
                    </div>
                </div>
                
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            onclick="closeEditModal({{ $comment->id }})"
                            class="px-6 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition-colors">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-6 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors font-medium">
                        <i class="fas fa-save mr-2"></i>Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

