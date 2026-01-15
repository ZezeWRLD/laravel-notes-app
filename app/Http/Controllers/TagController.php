<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;

class TagController extends Controller
{

    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $tag = Tag::firstOrFail($request->route('tag'));

        $notes = $tag->notes()
        ->with(['tags'])
        ->latest()
        ->paginate(20);

    return view('tags.show', [
        'tag' => $tag,
        'notes' => $notes,
    ]);
    }
}
