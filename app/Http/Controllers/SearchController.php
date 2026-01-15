<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Note;

class SearchController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $query = $request->query('q', '');

    $notes = Note::query()
        ->where('title', 'like', "%{$query}%")
        ->orWhere('content', 'like', "%{$query}%")
        ->with(['tags'])
        ->latest()
        ->paginate(20);

    return view('notes.search', [
        'notes' => $notes,
        'query' => $query,
    ]);
    }
}
