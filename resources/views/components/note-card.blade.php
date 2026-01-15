{{-- Simple note card --}}
<div class="note-card bg-white rounded-lg shadow-md p-6 mb-4">
    <div class="flex justify-between items-start">
        <div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">
                <a href="{{ route('notes.show', $note) }}" class="hover:text-note-primary transition">
                    {{ $note->title }}
                </a>
            </h3>
            <p class="text-gray-600 mb-4 line-clamp-3">
                {{ Str::limit($note->content, 200) }}
            </p>
        </div>
        <div class="flex space-x-2">
            @if($note->tags->count() > 0)
                @foreach($note->tags->take(3) as $tag)
                    <span class="inline-block bg-note-primary/10 text-note-primary text-xs px-2 py-1 rounded">
                        {{ $tag->name }}
                    </span>
                @endforeach
            @endif
        </div>
    </div>
    <div class="flex justify-between items-center text-sm text-gray-500">
        <span>{{ $note->created_at->diffForHumans() }}</span>
        <div class="flex space-x-4">
            <a href="{{ route('notes.edit', $note) }}" class="hover:text-note-primary">Edit</a>
            <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="hover:text-red-500"
                        onclick="return confirm('Delete this note?')">Delete</button>
            </form>
        </div>
    </div>
</div>
