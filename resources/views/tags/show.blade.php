@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 py-12">
        <div class="container mx-auto px-4 max-w-6xl">
            <div class="flex items-center gap-4 mb-4">
                <div class="w-12 h-12 bg-blue-500 rounded-lg flex items-center justify-center">
                    <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.999.999 0 111.414 9.293L10 17.879l8.293-8.586a1 1 0 011.414 0z"/>
                    </svg>
                </div>
                <h1 class="text-3xl font-bold text-blue-900">{{ $tag->name }}</h1>
            </div>
            <p class="text-lg text-blue-900 font-medium">
                {{ $notes->total() }} {{ Str::plural('note', $notes->total()) }} tagged with this label
            </p>

            <div class="mt-8 flex gap-4 flex-wrap">
                <a href="{{ route('notes.create') }}"
                   class="bg-blue-500 text-white px-6 py-3 rounded-lg font-bold
                          hover:bg-blue-700 transition shadow-lg">
                    + New Note
                </a>
                <a href="{{ route('notes.index') }}"
                   class="bg-white text-blue-600 px-6 py-3 rounded-lg font-bold
                          hover:bg-gray-100 transition shadow-lg border border-blue-600">
                    ← Back to Notes
                </a>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 max-w-6xl py-12">
        @if($notes->count() > 0)
            <!-- Tagged Notes -->
            <section>
                <div class="space-y-6">
                    @foreach($notes as $note)
                        <div class="bg-white rounded-xl shadow-md p-6 hover:shadow-lg transition-shadow">
                            <h3 class="text-2xl font-bold text-gray-950 mb-3">
                                <a href="{{ route('notes.show', $note) }}" class="hover:text-blue-600 transition">
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
                                    <a href="{{ route('notes.edit', $note) }}" class="text-blue-600 hover:text-blue-700 font-semibold transition">Edit</a>
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
                                    @foreach($note->tags as $noteTag)
                                        <a href="{{ route('tags.show', $noteTag) }}"
                                           class="inline-block {{ $noteTag->id === $tag->id ? 'bg-blue-600 text-white' : 'bg-blue-100 text-blue-600 hover:bg-blue-200' }}
                                                  transition px-3 py-1 rounded-full text-sm font-medium">
                                            {{ $noteTag->name }}
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
            <!-- No Notes for this Tag -->
            <div class="text-center py-12">
                <svg class="w-24 h-24 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                <h2 class="text-2xl font-bold text-gray-950 mb-2">No notes yet</h2>
                <p class="text-gray-700 mb-6">
                    There are no notes tagged with "<span class="font-semibold">{{ $tag->name }}</span>" yet.
                </p>
                <a href="{{ route('notes.create') }}" class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 transition">
                    Create First Note
                </a>
            </div>
        @endif
    </main>
</div>
@endsection
