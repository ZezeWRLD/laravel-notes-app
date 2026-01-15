{{-- Wide note card for dashboard --}}
<div class="note-card-wide bg-white rounded-xl shadow-lg p-8 mb-6 hover:shadow-xl transition-shadow">
    <div class="flex items-start gap-8">
        <!-- Left: Note Icon/Avatar -->
        <div class="flex-shrink-0">
            <div class="w-20 h-20 bg-gradient-to-br from-note-primary to-note-accent rounded-full
                        flex items-center justify-center text-white text-2xl">
                📝
            </div>
        </div>

        <!-- Center: Note Content -->
        <div class="flex-grow">
            <h3 class="text-2xl font-bold text-gray-900 mb-3">
                <a href="{{ route('notes.show', $note) }}" class="hover:text-note-primary transition">
                    {{ $note->title }}
                </a>
            </h3>
            <p class="text-gray-700 mb-6 leading-relaxed">
                {{ Str::limit($note->content, 300) }}
            </p>

            <!-- Note Metadata -->
            <div class="flex items-center space-x-6 text-sm text-gray-700 font-medium">
                <span class="flex items-center">
                    <svg class="w-4 h-4 mr-1 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                    </svg>
                    {{ $note->created_at->format('M j, Y') }}
                </span>
                @if($note->updated_at != $note->created_at)
                    <span class="flex items-center">
                        <svg class="w-4 h-4 mr-1 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                        </svg>
                        Updated {{ $note->updated_at->diffForHumans() }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Right: Tags -->
        @if($note->tags->count() > 0)
        <div class="flex-shrink-0">
            <div class="space-y-2">
                @foreach($note->tags as $tag)
                    <a href="{{ route('tags.show', $tag) }}"
                       class="block bg-note-secondary/10 text-note-secondary hover:bg-note-secondary/20
                              transition px-4 py-2 rounded-lg text-center text-sm font-medium">
                        {{ $tag->name }}
                    </a>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
