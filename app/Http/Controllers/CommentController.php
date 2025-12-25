<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\CommentLike;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Hanya user yang sudah login yang bisa berkomentar
        if (!auth()->check()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Anda harus login terlebih dahulu untuk berkomentar.'], 401);
            }
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu untuk berkomentar.');
        }
        
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'comment' => 'required|string|max:1000',
            'parent_id' => 'nullable|exists:comments,id',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan dalam mengirim komentar. Silakan periksa kembali data yang Anda masukkan.');
        }

        try {
            // Semua komentar langsung disetujui tanpa perlu persetujuan admin
            $autoApprove = true;
            
            // User sudah pasti login karena ada middleware auth
            $user = auth()->user();
            
            $comment = Comment::create([
                'article_id' => $request->article_id,
                'parent_id' => $request->parent_id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'comment' => $request->comment,
                'is_approved' => true, // Langsung disetujui
                'ip_address' => $request->ip(),
            ]);

            $comment->load('user');

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Komentar Anda telah berhasil dikirim dan ditampilkan.',
                    'comment' => [
                        'id' => $comment->id,
                        'name' => $comment->name,
                        'comment' => $comment->comment,
                        'parent_id' => $comment->parent_id,
                        'user_id' => $comment->user_id,
                        'likes_count' => $comment->likes_count ?? 0,
                        'dislikes_count' => $comment->dislikes_count ?? 0,
                        'created_at' => $comment->created_at,
                        'user' => $comment->user ? [
                            'id' => $comment->user->id,
                            'name' => $comment->user->name,
                            'username' => $comment->user->username ?? null,
                        ] : null,
                    ],
                    'is_approved' => true,
                ]);
            }

            return back()->with('success', 'Komentar Anda telah berhasil dikirim dan ditampilkan.');
        } catch (\Exception $e) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.'], 500);
            }
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.');
        }
    }

    public function like(Request $request, Comment $comment)
    {
        if (!auth()->check()) {
            return response()->json(['error' => 'Anda harus login terlebih dahulu.'], 401);
        }

        $user = auth()->user();
        $isLike = $request->input('is_like', true);

        DB::transaction(function () use ($comment, $user, $isLike) {
            $existingLike = CommentLike::where('comment_id', $comment->id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingLike) {
                // If clicking the same button, remove the like/dislike
                if ($existingLike->is_like == $isLike) {
                    $existingLike->delete();
                    if ($isLike) {
                        $comment->decrement('likes_count');
                    } else {
                        $comment->decrement('dislikes_count');
                    }
                } else {
                    // If clicking opposite button, switch the like/dislike
                    $existingLike->update(['is_like' => $isLike]);
                    if ($isLike) {
                        $comment->decrement('dislikes_count');
                        $comment->increment('likes_count');
                    } else {
                        $comment->decrement('likes_count');
                        $comment->increment('dislikes_count');
                    }
                }
            } else {
                // Create new like/dislike
                CommentLike::create([
                    'comment_id' => $comment->id,
                    'user_id' => $user->id,
                    'is_like' => $isLike,
                ]);
                if ($isLike) {
                    $comment->increment('likes_count');
                } else {
                    $comment->increment('dislikes_count');
                }
            }
        });

        $comment->refresh();

        return response()->json([
            'success' => true,
            'likes_count' => $comment->likes_count,
            'dislikes_count' => $comment->dislikes_count,
            'is_liked' => $comment->isLikedByUser(),
            'is_disliked' => $comment->isDislikedByUser(),
        ]);
    }

    public function update(Request $request, Comment $comment)
    {
        if (!auth()->check() || $comment->user_id !== auth()->id()) {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Anda tidak memiliki izin untuk mengedit komentar ini.'], 403);
            }
            return back()->with('error', 'Anda tidak memiliki izin untuk mengedit komentar ini.');
        }

        $validator = Validator::make($request->all(), [
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['errors' => $validator->errors()], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        $comment->update([
            'comment' => $request->comment,
            'is_approved' => true, // Langsung disetujui setelah edit
        ]);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil diperbarui.',
                'comment' => $comment,
            ]);
        }

        return back()->with('success', 'Komentar berhasil diperbarui.');
    }

    public function destroy(Comment $comment)
    {
        if (!auth()->check() || $comment->user_id !== auth()->id()) {
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Anda tidak memiliki izin untuk menghapus komentar ini.'], 403);
            }
            return back()->with('error', 'Anda tidak memiliki izin untuk menghapus komentar ini.');
        }

        $comment->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Komentar berhasil dihapus.',
            ]);
        }

        return back()->with('success', 'Komentar berhasil dihapus.');
    }
}
