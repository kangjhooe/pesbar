@php
    $isOwner = auth()->check() && $comment->user_id === auth()->id();
    $isLiked = $comment->isLikedByUser();
    $isDisliked = $comment->isDislikedByUser();
@endphp

<div class="comment-item {{ $level > 0 ? 'ml-8 mt-4' : '' }} mb-4 bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition-all duration-200 overflow-hidden" 
     data-comment-id="{{ $comment->id }}" 
     id="comment-{{ $comment->id }}">
    <div class="p-4 md:p-5">
        <div class="flex items-start gap-4">
            <!-- Avatar -->
            <div class="flex-shrink-0">
                @if($comment->user && $comment->user->profile && $comment->user->profile->avatar)
                    <img src="{{ asset('storage/' . $comment->user->profile->avatar) }}" 
                         alt="{{ $comment->name }}" 
                         class="w-12 h-12 rounded-full object-cover border-2 border-primary-200 shadow-sm hover:border-primary-400 transition-colors">
                @else
                    <div class="w-12 h-12 bg-gradient-to-br from-primary-500 to-primary-600 rounded-full flex items-center justify-center text-white font-bold text-lg shadow-md hover:shadow-lg transition-shadow">
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
                            <h5 class="font-bold text-gray-900 text-base">{{ $comment->name }}</h5>
                            @if($comment->user)
                                <x-user-role-badge :user="$comment->user" size="xs" />
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-wrap text-xs text-gray-500">
                            @if($comment->user_id && $comment->user && $comment->user->username)
                                <a href="{{ route('penulis.public-profile', $comment->user->username) }}" 
                                   class="text-primary-600 hover:text-primary-800 font-medium flex items-center gap-1 transition-colors hover:underline">
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
                        <button onclick="editComment({{ $comment->id }}, '{{ addslashes($comment->comment) }}')" 
                                class="p-2 text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded-lg transition-all duration-200" 
                                title="Edit Komentar">
                            <i class="fas fa-edit text-sm"></i>
                        </button>
                        <form action="{{ route('comments.destroy', $comment) }}" 
                              method="POST" 
                              class="inline" 
                              onsubmit="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');">
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
                    <p class="text-gray-800 leading-relaxed whitespace-pre-wrap text-[15px]">{{ $comment->comment }}</p>
                </div>
                
                <!-- Actions -->
                <div class="flex items-center gap-3 pt-3 border-t border-gray-100">
                    <!-- Like/Dislike -->
                    <div class="flex items-center gap-2">
                        <button onclick="toggleLike({{ $comment->id }}, true)" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-200 {{ $isLiked ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-gray-50 text-gray-600 hover:bg-green-50 hover:text-green-700 border border-gray-200 hover:border-green-200' }}"
                                id="like-btn-{{ $comment->id }}"
                                title="Suka">
                            <i class="fas fa-thumbs-up text-sm"></i>
                            <span class="text-sm font-medium" id="likes-count-{{ $comment->id }}">{{ $comment->likes_count ?? 0 }}</span>
                        </button>
                        <button onclick="toggleLike({{ $comment->id }}, false)" 
                                class="flex items-center gap-2 px-3 py-1.5 rounded-lg transition-all duration-200 {{ $isDisliked ? 'bg-red-50 text-red-700 border border-red-200' : 'bg-gray-50 text-gray-600 hover:bg-red-50 hover:text-red-700 border border-gray-200 hover:border-red-200' }}"
                                id="dislike-btn-{{ $comment->id }}"
                                title="Tidak suka">
                            <i class="fas fa-thumbs-down text-sm"></i>
                            <span class="text-sm font-medium" id="dislikes-count-{{ $comment->id }}">{{ $comment->dislikes_count ?? 0 }}</span>
                        </button>
                    </div>
                    
                    <!-- Reply Button -->
                    @auth
                    <button onclick="replyToComment({{ $comment->id }}, '{{ addslashes($comment->name) }}')" 
                            class="flex items-center gap-2 px-3 py-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 font-medium rounded-lg transition-all duration-200 border border-transparent hover:border-primary-200">
                        <i class="fas fa-reply text-sm"></i>
                        <span class="text-sm">Balas</span>
                    </button>
                    @else
                    <a href="{{ route('login') }}" 
                       class="flex items-center gap-2 px-3 py-1.5 text-primary-600 hover:text-primary-800 hover:bg-primary-50 font-medium rounded-lg transition-all duration-200 border border-transparent hover:border-primary-200">
                        <i class="fas fa-reply text-sm"></i>
                        <span class="text-sm">Balas</span>
                    </a>
                    @endauth
                </div>
            </div>
            
            <!-- Replies -->
            @if($comment->replies->count() > 0)
                <div class="mt-5 pt-4 border-t border-gray-100 replies-container">
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

