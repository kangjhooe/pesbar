<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;

class PublishScheduledArticles extends Command
{
    protected $signature = 'articles:publish-scheduled';

    protected $description = 'Terbitkan artikel jadwal yang sudah jatuh tempo (draft + scheduled_at <= sekarang)';

    public function handle(): int
    {
        $due = Article::query()
            ->where('status', 'draft')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->with('author')
            ->get();

        if ($due->isEmpty()) {
            $this->info('Tidak ada artikel jadwal yang jatuh tempo.');
            return self::SUCCESS;
        }

        $published = 0;
        $held = 0;

        foreach ($due as $article) {
            $author = $article->author;

            // Author hilang / bukan penulis / banned / publish dibatasi → tetap draft, jangan antre review
            if (!$author || !$author->isPenulis() || $author->isBanned() || !$author->canPublishDirectly()) {
                $held++;
                continue;
            }

            $article->update([
                'status' => 'published',
                'published_at' => $article->scheduled_at ?? now(),
                'scheduled_at' => null,
            ]);
            $published++;
        }

        $this->info("Terbit: {$published}; ditahan (draft): {$held}.");

        return self::SUCCESS;
    }
}
