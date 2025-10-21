<?php

namespace App\Services;

use App\Models\Poll;
use App\Models\PollOption;
use App\Models\PollVote;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\Auth;

class PollService
{
    private const CACHE_KEY = 'polls_widget';
    private const CACHE_DURATION = 1800; // 30 menit

    /**
     * Get active poll for widget display
     */
    public function getActivePoll()
    {
        $cacheKey = self::CACHE_KEY . '_active';
        
        return CacheHelper::remember(
            $cacheKey,
            self::CACHE_DURATION,
            function () {
                return $this->fetchActivePoll();
            }
        );
    }

    /**
     * Fetch active poll
     */
    private function fetchActivePoll()
    {
        $poll = Poll::running()
            ->with(['options' => function($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->orderBy('created_at', 'desc')
            ->first();

        if (!$poll) {
            return null;
        }

        // Add vote counts and percentages
        $poll->load(['options.votes']);
        
        return [
            'poll' => $poll,
            'updated_at' => now()->format('H:i')
        ];
    }

    /**
     * Get all running polls
     */
    public function getRunningPolls($limit = 5)
    {
        return Poll::running()
            ->with(['options' => function($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get finished polls
     */
    public function getFinishedPolls($limit = 5)
    {
        return Poll::finished()
            ->with(['options' => function($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->orderBy('end_date', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get upcoming polls
     */
    public function getUpcomingPolls($limit = 5)
    {
        return Poll::upcoming()
            ->with(['options' => function($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->orderBy('start_date', 'asc')
            ->limit($limit)
            ->get();
    }

    /**
     * Submit vote
     */
    public function submitVote($pollId, $optionIds, $userId = null, $ipAddress = null, $userAgent = null)
    {
        $poll = Poll::findOrFail($pollId);
        
        // Check if user can vote
        if (!$poll->canUserVote($userId, $ipAddress)) {
            return [
                'success' => false,
                'message' => 'Anda sudah memberikan suara untuk polling ini'
            ];
        }

        // Validate option IDs
        $validOptionIds = $poll->options()->active()->pluck('id')->toArray();
        $optionIds = is_array($optionIds) ? $optionIds : [$optionIds];
        
        foreach ($optionIds as $optionId) {
            if (!in_array($optionId, $validOptionIds)) {
                return [
                    'success' => false,
                    'message' => 'Pilihan tidak valid'
                ];
            }
        }

        // Check max votes per user
        if (count($optionIds) > $poll->max_votes_per_user) {
            return [
                'success' => false,
                'message' => 'Jumlah pilihan melebihi batas yang diizinkan'
            ];
        }

        try {
            // Create votes
            foreach ($optionIds as $optionId) {
                PollVote::create([
                    'poll_id' => $pollId,
                    'poll_option_id' => $optionId,
                    'user_id' => $userId,
                    'ip_address' => $ipAddress,
                    'user_agent' => $userAgent
                ]);
            }

            // Clear cache
            CacheHelper::forget(self::CACHE_KEY . '_active');

            return [
                'success' => true,
                'message' => 'Suara Anda telah direkam'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan suara'
            ];
        }
    }

    /**
     * Get poll results
     */
    public function getPollResults($pollId)
    {
        $poll = Poll::with(['options.votes'])->findOrFail($pollId);
        
        if (!$poll->show_results) {
            return [
                'success' => false,
                'message' => 'Hasil polling tidak ditampilkan'
            ];
        }

        $results = [];
        foreach ($poll->options as $option) {
            $results[] = [
                'id' => $option->id,
                'text' => $option->option_text,
                'color' => $option->color,
                'vote_count' => $option->vote_count,
                'percentage' => $option->vote_percentage
            ];
        }

        return [
            'success' => true,
            'poll' => $poll,
            'results' => $results,
            'total_votes' => $poll->total_votes
        ];
    }

    /**
     * Get poll statistics
     */
    public function getPollStats()
    {
        $totalPolls = Poll::active()->count();
        $runningPolls = Poll::running()->count();
        $finishedPolls = Poll::finished()->count();
        $upcomingPolls = Poll::upcoming()->count();

        return [
            'total' => $totalPolls,
            'running' => $runningPolls,
            'finished' => $finishedPolls,
            'upcoming' => $upcomingPolls
        ];
    }

    /**
     * Get user's voting history
     */
    public function getUserVotingHistory($userId, $limit = 10)
    {
        return PollVote::where('user_id', $userId)
            ->with(['poll', 'pollOption'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Check if user has voted in poll
     */
    public function hasUserVoted($pollId, $userId = null, $ipAddress = null)
    {
        $poll = Poll::findOrFail($pollId);
        return !$poll->canUserVote($userId, $ipAddress);
    }

    /**
     * Get popular polls (by vote count)
     */
    public function getPopularPolls($limit = 5)
    {
        return Poll::active()
            ->with(['options' => function($query) {
                $query->active()->orderBy('sort_order');
            }])
            ->withCount('votes')
            ->orderBy('votes_count', 'desc')
            ->limit($limit)
            ->get();
    }
}
