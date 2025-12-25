<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Helpers\SettingsHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        // Hanya user yang sudah login yang bisa berkomentar
        if (!auth()->check()) {
            return redirect()->route('login')
                ->with('error', 'Anda harus login terlebih dahulu untuk berkomentar.');
        }
        
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan dalam mengirim komentar. Silakan periksa kembali data yang Anda masukkan.');
        }

        try {
            // Cek apakah auto approve diaktifkan
            $autoApprove = SettingsHelper::autoApproveComments();
            
            // User sudah pasti login karena ada middleware auth
            $user = auth()->user();
            
            $comment = Comment::create([
                'article_id' => $request->article_id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'comment' => $request->comment,
                'is_approved' => $autoApprove, // Gunakan setting auto approve
                'ip_address' => $request->ip(),
            ]);

            $message = $autoApprove 
                ? 'Komentar Anda telah berhasil dikirim dan ditampilkan.'
                : 'Komentar Anda telah dikirim dan sedang menunggu persetujuan admin.';

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.');
        }
    }
}
