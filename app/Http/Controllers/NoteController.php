<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NoteNotification;

class NoteController extends Controller
{
    use AuthorizesRequests;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Get notes for logged-in user
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $notes = $user->notes()->latest()->get();

        // Split notes into featured and recent
        $featuredNotes = $user->notes()->where('featured', true)->latest()->take(3)->get();
        $recentNotes = $user->notes()->latest()->take(6)->get();

        // Get all tags associated with user's notes, with count
        $tags = \App\Models\Tag::whereHas('notes', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->withCount('notes')->get();

        return view('notes.index', compact('notes', 'featuredNotes', 'recentNotes', 'tags'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('notes.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $note = $user->notes()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        // Process tags if provided
        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagNames = array_filter($tagNames); // Remove empty strings

            foreach ($tagNames as $tagName) {
                $note->tag($tagName);
            }
        }

        try {
            Mail::to(auth()->user()->email)->send(new NoteNotification($note, 'created'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send note creation notification', [
                'user_id' => auth()->id(),
                'note_id' => $note->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('notes.show', $note)
            ->with('success', 'Note created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Note $note)
    {
        // Authorization - user can only view their own notes
        $this->authorize('view', $note);

        return view('notes.show', compact('note'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Note $note)
    {
        // Authorization - user can only edit their own notes
        $this->authorize('update', $note);

        return view('notes.edit', compact('note'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Note $note)
    {
        $this->authorize('update', $note);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'nullable|string',
            'tags' => 'nullable|string',
        ]);

        $note->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        // Process tags if provided
        if ($request->filled('tags')) {
            $tagNames = array_map('trim', explode(',', $request->input('tags')));
            $tagNames = array_filter($tagNames); // Remove empty strings

            // Clear existing tags and attach new ones
            $note->tags()->detach();

            foreach ($tagNames as $tagName) {
                $note->tag($tagName);
            }
        } else {
            // If no tags provided, clear all tags
            $note->tags()->detach();
        }

        try {
            Mail::to(auth()->user()->email)->send(new NoteNotification($note, 'updated'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send note update notification', [
                'user_id' => auth()->id(),
                'note_id' => $note->id,
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('notes.show', $note)
            ->with('success', 'Note updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        // Authorization - user can only delete their own notes
        $this->authorize('delete', $note);

        // Store note data before deletion
        $noteData = $note->toArray();
        $note->delete();

        // Create a dummy note object for the email
        $dummyNote = (object) [
            'id' => $noteData['id'],
            'title' => $noteData['title'],
        ];

        // Send email - exactly like tutorial
        try {
            Mail::to(auth()->user()->email)->send(new NoteNotification($dummyNote, 'deleted'));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to send note deletion notification', [
                'user_id' => auth()->id(),
                'note_id' => $noteData['id'],
                'error' => $e->getMessage(),
            ]);
        }

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }
}
