<?php

namespace App\Http\Controllers;

use App\Models\EventPopup;
use Illuminate\Http\Request;
use Carbon\Carbon;

class EventPopupController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $eventPopups = EventPopup::orderBy('created_at', 'desc')->paginate(10);
        return view('admin.event-popups.index', compact('eventPopups'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.event-popups.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'boolean'
        ]);

        EventPopup::create([
            'title' => $request->title,
            'message' => $request->message,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->has('status')
        ]);

        return redirect()->route('admin.event-popups.index')
                        ->with('success', 'Event popup berhasil dibuat!');
    }

    /**
     * Display the specified resource.
     */
    public function show(EventPopup $eventPopup)
    {
        return view('admin.event-popups.show', compact('eventPopup'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(EventPopup $eventPopup)
    {
        return view('admin.event-popups.edit', compact('eventPopup'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, EventPopup $eventPopup)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'boolean'
        ]);

        $eventPopup->update([
            'title' => $request->title,
            'message' => $request->message,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => $request->has('status')
        ]);

        return redirect()->route('admin.event-popups.index')
                        ->with('success', 'Event popup berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(EventPopup $eventPopup)
    {
        $eventPopup->delete();

        return redirect()->route('admin.event-popups.index')
                        ->with('success', 'Event popup berhasil dihapus!');
    }

    /**
     * Toggle status popup
     */
    public function toggleStatus(EventPopup $eventPopup)
    {
        $eventPopup->update(['status' => !$eventPopup->status]);
        
        $status = $eventPopup->status ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('admin.event-popups.index')
                        ->with('success', "Event popup berhasil {$status}!");
    }
}
