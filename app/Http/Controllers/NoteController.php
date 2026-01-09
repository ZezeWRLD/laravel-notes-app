<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        return view('notes.index', compact('notes'));
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
            'content' => 'nullable|string', // CHANGED
        ]);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        $user->notes()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('notes.index')
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
    ]);

    $note->update([
        'title' => $request->input('title'),
        'content' => $request->input('content'),
    ]);

    return redirect()->route('notes.index')
        ->with('success', 'Note updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Note $note)
    {
        // Authorization - user can only delete their own notes
        $this->authorize('delete', $note);

        $note->delete();

        return redirect()->route('notes.index')
            ->with('success', 'Note deleted successfully.');
    }
}
