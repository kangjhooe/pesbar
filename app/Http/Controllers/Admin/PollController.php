<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Poll;
use App\Models\PollOption;
use Illuminate\Http\Request;
use Carbon\Carbon;

class PollController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $polls = Poll::with(['options'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Poll::count(),
            'active' => Poll::active()->count(),
            'running' => Poll::running()->count(),
            'finished' => Poll::finished()->count()
        ];

        return view('admin.polls.index', compact('polls', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.polls.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poll_type' => 'required|in:single,multiple',
            'is_active' => 'boolean',
            'allow_anonymous' => 'boolean',
            'show_results' => 'boolean',
            'show_vote_count' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_votes_per_user' => 'required|integer|min:1|max:10',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|string|max:255',
            'options.*.color' => 'required|string|max:7',
            'options.*.description' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Convert boolean fields
        $data['is_active'] = $request->has('is_active');
        $data['allow_anonymous'] = $request->has('allow_anonymous');
        $data['show_results'] = $request->has('show_results');
        $data['show_vote_count'] = $request->has('show_vote_count');

        // Create poll
        $poll = Poll::create($data);

        // Create poll options
        foreach ($request->options as $index => $option) {
            PollOption::create([
                'poll_id' => $poll->id,
                'option_text' => $option['text'],
                'description' => $option['description'] ?? null,
                'color' => $option['color'],
                'sort_order' => $index + 1,
                'is_active' => true
            ]);
        }

        return redirect()->route('admin.polls.index')
            ->with('success', 'Polling berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Poll $poll)
    {
        $poll->load(['options.votes']);
        
        $results = [];
        foreach ($poll->options as $option) {
            $results[] = [
                'option' => $option,
                'vote_count' => $option->votes->count(),
                'percentage' => $poll->total_votes > 0 ? round(($option->votes->count() / $poll->total_votes) * 100, 1) : 0
            ];
        }

        return view('admin.polls.show', compact('poll', 'results'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Poll $poll)
    {
        $poll->load('options');
        return view('admin.polls.edit', compact('poll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Poll $poll)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'poll_type' => 'required|in:single,multiple',
            'is_active' => 'boolean',
            'allow_anonymous' => 'boolean',
            'show_results' => 'boolean',
            'show_vote_count' => 'boolean',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'max_votes_per_user' => 'required|integer|min:1|max:10',
            'options' => 'required|array|min:2',
            'options.*.id' => 'nullable|exists:poll_options,id',
            'options.*.text' => 'required|string|max:255',
            'options.*.color' => 'required|string|max:7',
            'options.*.description' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Convert boolean fields
        $data['is_active'] = $request->has('is_active');
        $data['allow_anonymous'] = $request->has('allow_anonymous');
        $data['show_results'] = $request->has('show_results');
        $data['show_vote_count'] = $request->has('show_vote_count');

        // Update poll
        $poll->update($data);

        // Update poll options
        $existingOptionIds = [];
        foreach ($request->options as $index => $optionData) {
            if (isset($optionData['id'])) {
                // Update existing option
                $option = PollOption::find($optionData['id']);
                if ($option) {
                    $option->update([
                        'option_text' => $optionData['text'],
                        'description' => $optionData['description'] ?? null,
                        'color' => $optionData['color'],
                        'sort_order' => $index + 1
                    ]);
                    $existingOptionIds[] = $option->id;
                }
            } else {
                // Create new option
                $option = PollOption::create([
                    'poll_id' => $poll->id,
                    'option_text' => $optionData['text'],
                    'description' => $optionData['description'] ?? null,
                    'color' => $optionData['color'],
                    'sort_order' => $index + 1,
                    'is_active' => true
                ]);
                $existingOptionIds[] = $option->id;
            }
        }

        // Delete options that are no longer in the request
        $poll->options()->whereNotIn('id', $existingOptionIds)->delete();

        return redirect()->route('admin.polls.index')
            ->with('success', 'Polling berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Poll $poll)
    {
        // Delete all votes and options (cascade)
        $poll->delete();

        return redirect()->route('admin.polls.index')
            ->with('success', 'Polling berhasil dihapus!');
    }

    /**
     * Toggle poll status
     */
    public function toggleStatus(Poll $poll)
    {
        $poll->update(['is_active' => !$poll->is_active]);
        
        $status = $poll->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Polling berhasil {$status}!");
    }

    /**
     * Reset poll votes
     */
    public function resetVotes(Poll $poll)
    {
        $poll->votes()->delete();
        
        return redirect()->back()
            ->with('success', 'Suara polling berhasil direset!');
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete,reset_votes',
            'poll_ids' => 'required|array',
            'poll_ids.*' => 'exists:polls,id'
        ]);

        $polls = Poll::whereIn('id', $request->poll_ids);

        switch ($request->action) {
            case 'activate':
                $polls->update(['is_active' => true]);
                $message = 'Polling berhasil diaktifkan!';
                break;
            case 'deactivate':
                $polls->update(['is_active' => false]);
                $message = 'Polling berhasil dinonaktifkan!';
                break;
            case 'delete':
                $polls->delete();
                $message = 'Polling berhasil dihapus!';
                break;
            case 'reset_votes':
                foreach ($polls->get() as $poll) {
                    $poll->votes()->delete();
                }
                $message = 'Suara polling berhasil direset!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}