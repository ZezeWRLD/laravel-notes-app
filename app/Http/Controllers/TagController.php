<?php

namespace App\Http\Controllers;

use App\Models\Tag;

class TagController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Tag $tag)
    {
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
