<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::orderBy('event_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $stats = [
            'total' => Event::count(),
            'active' => Event::active()->count(),
            'upcoming' => Event::active()->upcoming()->count(),
            'today' => Event::active()->today()->count()
        ];

        return view('admin.events.index', compact('events', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date|after_or_equal:today',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'event_type' => 'required|in:pemerintah,masyarakat,budaya,olahraga,pendidikan,kesehatan,lainnya',
            'priority' => 'required|in:low,medium,high',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_info' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        // Convert boolean fields
        $data['is_public'] = $request->has('is_public');
        $data['is_active'] = $request->has('is_active');

        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Event $event)
    {
        return view('admin.events.show', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Event $event)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'event_date' => 'required|date',
            'start_time' => 'nullable|date_format:H:i',
            'end_time' => 'nullable|date_format:H:i|after:start_time',
            'location' => 'nullable|string|max:255',
            'organizer' => 'nullable|string|max:255',
            'event_type' => 'required|in:pemerintah,masyarakat,budaya,olahraga,pendidikan,kesehatan,lainnya',
            'priority' => 'required|in:low,medium,high',
            'is_public' => 'boolean',
            'is_active' => 'boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'contact_info' => 'nullable|string'
        ]);

        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            $data['image'] = $request->file('image')->store('events', 'public');
        }

        // Convert boolean fields
        $data['is_public'] = $request->has('is_public');
        $data['is_active'] = $request->has('is_active');

        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event)
    {
        // Delete image if exists
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil dihapus!');
    }

    /**
     * Toggle event status
     */
    public function toggleStatus(Event $event)
    {
        $event->update(['is_active' => !$event->is_active]);
        
        $status = $event->is_active ? 'diaktifkan' : 'dinonaktifkan';
        
        return redirect()->back()
            ->with('success', "Event berhasil {$status}!");
    }

    /**
     * Bulk actions
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:activate,deactivate,delete',
            'event_ids' => 'required|array',
            'event_ids.*' => 'exists:events,id'
        ]);

        $events = Event::whereIn('id', $request->event_ids);

        switch ($request->action) {
            case 'activate':
                $events->update(['is_active' => true]);
                $message = 'Event berhasil diaktifkan!';
                break;
            case 'deactivate':
                $events->update(['is_active' => false]);
                $message = 'Event berhasil dinonaktifkan!';
                break;
            case 'delete':
                // Delete images
                $events->get()->each(function ($event) {
                    if ($event->image) {
                        Storage::disk('public')->delete($event->image);
                    }
                });
                $events->delete();
                $message = 'Event berhasil dihapus!';
                break;
        }

        return redirect()->back()->with('success', $message);
    }
}