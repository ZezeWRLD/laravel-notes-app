@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <h1 class="text-3xl font-bold mb-2 text-blue-900">Search Results</h1>
            <p class="text-lg text-blue-900 font-medium">
                @if($query)
                    Results for "<span class="font-bold">{{ $query }}</span>"
                @else
                    Enter a search term to find notes
                @endif
            </p>

            <div class="mt-8 flex gap-4 flex-wrap">
                <a href="{{ route('notes.create') }}"
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg font-bold
                          hover:bg-blue-600 transition shadow-lg">
                    + New Note
                </a>
                <form action="{{ route('search') }}" method="GET" class="flex-grow max-w-md">
                    <div class="flex gap-2">
                        <input type="search"
                               name="q"
                               placeholder="Search notes..."
                               value="{{ $query }}"
                               class="flex-grow px-4 py-3 rounded-lg text-gray-900 placeholder-gray-500 focus:outline-none
                                      focus:ring-2 focus:ring-blue-400">
                        <button type="submit"
                                class="bg-blue-500 text-white px-4 py-3 rounded-lg font-bold
                                       hover:bg-blue-600 transition shadow-lg">
                            Search
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 max-w-6xl py-12">
        @if($query)
            @if($notes->count() > 0)
                <!-- Search Results -->
                <section>
                    <div class="mb-8">
                        <h2 class="text-2xl font-bold text-gray-950">
                            Found {{ $notes->total() }} {{ Str::plural('result', $notes->total()) }}
                        </h2>
                    </div>

                    <div class="space-y-6">
                        @foreach($notes as $note)
                            <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                                <h3 class="text-2xl font-bold text-gray-950 mb-3">
                                    <a href="{{ route('notes.show', $note) }}" class="hover:text-note-primary transition">
                                        {{ $note->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-700 mb-4 leading-relaxed">
                                    {{ Str::limit($note->content, 300) }}
                                </p>

                                <!-- Note Metadata -->
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-4 text-sm text-gray-700 font-medium">
                                        <span>{{ $note->created_at->format('M j, Y') }}</span>
                                        @if($note->updated_at != $note->created_at)
                                            <span>Updated {{ $note->updated_at->diffForHumans() }}</span>
                                        @endif
                                    </div>
                                    <div class="flex space-x-4">
                                        <a href="{{ route('notes.edit', $note) }}" class="text-note-primary hover:text-note-accent font-semibold transition">Edit</a>
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-700 font-semibold transition"
                                                    onclick="return confirm('Delete this note?')">Delete</button>
                                        </form>
                                    </div>
                                </div>

                                <!-- Tags -->
                                @if($note->tags->count() > 0)
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        @foreach($note->tags as $tag)
                                            <a href="{{ route('tags.show', $tag) }}"
                                               class="inline-block bg-note-primary/10 text-note-primary hover:bg-note-primary/20
                                                      transition px-3 py-1 rounded-full text-sm font-medium">
                                                {{ $tag->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    <div class="mt-12">
                        {{ $notes->links() }}
                    </div>
                </section>
            @else
                <!-- No Results -->
                <div class="text-center py-12">
                    <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-950 mb-2">No results found</h2>
                    <p class="text-gray-700 mb-6">
                        Sorry, no notes match your search for "<span class="font-semibold">{{ $query }}</span>". Try a different search term.
                    </p>
                    <a href="{{ route('notes.index') }}" class="inline-block bg-note-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-note-accent transition">
                        Back to Notes
                    </a>
                </div>
            @endif
        @else
            <!-- No Search Query -->
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-950 mb-2">Search Your Notes</h2>
                <p class="text-gray-700 mb-6">
                    Enter a keyword above to search through your notes by title or content.
                </p>
                <a href="{{ route('notes.index') }}" class="inline-block bg-note-primary text-white px-6 py-3 rounded-lg font-semibold hover:bg-note-accent transition">
                    Back to Notes
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
