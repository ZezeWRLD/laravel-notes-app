@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-blue-600 py-16">
        <div class="container mx-auto px-4 max-w-6xl">
            <h1 class="text-4xl font-bold mb-4 text-blue-900">Your Notes Dashboard</h1>
            <p class="text-lg text-blue-900 font-medium">Organize, create, and manage your thoughts</p>

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
                               value="{{ request('q', '') }}"
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
        <!-- Featured Notes Section -->
        <section class="mb-16">
            <div class="mb-8">
                <h2 class="text-3xl font-bold text-gray-950">Featured Notes</h2>
            </div>

            <div class="space-y-6">
                @foreach($featuredNotes as $note)
                    <x-note-card-wide :note="$note" />
                @endforeach
            </div>
        </section>

        <!-- Recent Notes Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-gray-950 mb-8">Recent Notes</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentNotes as $note)
                    <x-note-card :note="$note" />
                @endforeach
            </div>
        </section>

        <!-- Tags Section -->
        <section>
            <h2 class="text-3xl font-bold text-gray-950 mb-8">Your Tags</h2>

            <x-panel>
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <x-tag :href="route('tags.show', $tag)" size="lg">
                            {{ $tag->name }} ({{ $tag->notes_count }})
                        </x-tag>
                    @endforeach

                    @if($tags->isEmpty())
                        <p class="text-gray-700 font-medium">No tags yet. Add tags to your notes!</p>
                    @endif
                </div>
            </x-panel>
        </section>
    </main>
</div>
@endsection
