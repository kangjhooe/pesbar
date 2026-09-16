<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\ArticleReport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;

class ArticleReportController extends Controller
{
    public function store(Request $request, Article $article)
    {
        if ($request->filled('website')) {
            return $this->okResponse($request, 'Laporan Anda telah dikirim. Terima kasih.');
        }

        if ($article->status !== 'published') {
            return $this->failResponse($request, 'Artikel ini tidak dapat dilaporkan.', 422);
        }

        $key = 'article-report:' . $request->ip() . ':' . $article->id;
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            return $this->failResponse($request, "Terlalu banyak laporan. Coba lagi dalam {$seconds} detik.", 429);
        }

        $isGuest = !auth()->check();

        $rules = [
            'reason' => 'required|string|in:' . implode(',', array_keys(ArticleReport::REASONS)),
            'details' => 'nullable|string|max:1000',
        ];

        if ($isGuest) {
            $rules['name'] = 'required|string|max:100';
            $rules['email'] = 'required|email|max:100';
        }

        $validator = Validator::make($request->all(), $rules, [
            'reason.required' => 'Pilih alasan laporan.',
            'name.required' => 'Nama wajib diisi.',
            'email.required' => 'Email wajib diisi.',
        ]);

        if ($validator->fails()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }

            return back()->withErrors($validator)->withInput()->with('error', 'Periksa kembali formulir laporan.');
        }

        $user = auth()->user();

        // Cegah spam duplikat laporan terbuka dari user/guest yang sama
        $dupQuery = ArticleReport::query()
            ->where('article_id', $article->id)
            ->where('status', 'open');

        if ($user) {
            $dupQuery->where('user_id', $user->id);
        } else {
            $dupQuery->whereNull('user_id')->where('email', $request->email);
        }

        if ($dupQuery->exists()) {
            return $this->okResponse($request, 'Laporan Anda sudah tercatat dan sedang ditinjau.');
        }

        ArticleReport::create([
            'article_id' => $article->id,
            'user_id' => $user?->id,
            'name' => $isGuest ? $request->name : $user->name,
            'email' => $isGuest ? $request->email : $user->email,
            'reason' => $request->reason,
            'details' => $request->details,
            'status' => 'open',
            'ip_address' => $request->ip(),
        ]);

        RateLimiter::hit($key, 600);

        return $this->okResponse($request, 'Laporan Anda telah dikirim. Terima kasih.');
    }

    private function okResponse(Request $request, string $message)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => $message]);
        }

        return back()->with('success', $message);
    }

    private function failResponse(Request $request, string $message, int $status = 422)
    {
        if ($request->expectsJson()) {
            return response()->json(['success' => false, 'message' => $message], $status);
        }

        return back()->with('error', $message);
    }
}
