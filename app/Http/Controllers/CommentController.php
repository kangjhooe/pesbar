<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'article_id' => 'required|exists:articles,id',
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:100',
            'comment' => 'required|string|max:1000',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Terjadi kesalahan dalam mengirim komentar. Silakan periksa kembali data yang Anda masukkan.');
        }

        try {
            Comment::create([
                'article_id' => $request->article_id,
                'name' => $request->name,
                'email' => $request->email,
                'comment' => $request->comment,
                'is_approved' => false, // Komentar perlu disetujui admin
                'ip_address' => $request->ip(),
            ]);

            return back()->with('success', 'Komentar Anda telah dikirim dan sedang menunggu persetujuan admin.');
        } catch (\Exception $e) {
            return back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengirim komentar. Silakan coba lagi.');
        }
    }
}
