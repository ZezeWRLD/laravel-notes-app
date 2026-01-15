@extends('layouts.app')

@push('styles')
    @vite(['resources/css/notes.css'])
@endpush

@section('content')
<div class="min-h-screen bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-note-primary to-note-accent text-white py-16">
        <div class="container mx-auto px-4 max-w-6xl">
            <h1 class="text-4xl font-bold mb-4">Your Notes Dashboard</h1>
            <p class="text-xl opacity-90">Organize, create, and manage your thoughts</p>

            <div class="mt-8 flex space-x-4">
                <a href="{{ route('notes.create') }}"
                   class="bg-white text-note-primary px-6 py-3 rounded-lg font-semibold
                          hover:bg-gray-100 transition shadow-lg">
                    + New Note
                </a>
                <div class="relative flex-grow max-w-md">
                    <input type="search"
                           placeholder="Search notes..."
                           class="w-full px-4 py-3 rounded-lg text-gray-900 focus:outline-none
                                  focus:ring-2 focus:ring-white/50">
                </div>
            </div>
        </div>
    </header>

    <main class="container mx-auto px-4 max-w-6xl py-12">
        <!-- Featured Notes Section -->
        <section class="mb-16">
            <div class="flex justify-between items-center mb-8">
                <h2 class="text-3xl font-bold text-gray-900">Featured Notes</h2>
                <a href="{{ route('notes.index') }}" class="text-note-primary hover:underline">
                    View all notes →
                </a>
            </div>

            <div class="space-y-6">
                @foreach($featuredNotes as $note)
                    <x-note-card-wide :note="$note" />
                @endforeach
            </div>
        </section>

        <!-- Recent Notes Section -->
        <section class="mb-16">
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Recent Notes</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($recentNotes as $note)
                    <x-note-card :note="$note" />
                @endforeach
            </div>
        </section>

        <!-- Tags Section -->
        <section>
            <h2 class="text-3xl font-bold text-gray-900 mb-8">Your Tags</h2>

            <x-panel>
                <div class="flex flex-wrap gap-3">
                    @foreach($tags as $tag)
                        <x-tag :href="route('tags.show', $tag)" size="lg">
                            {{ $tag->name }} ({{ $tag->notes_count }})
                        </x-tag>
                    @endforeach

                    @if($tags->isEmpty())
                        <p class="text-gray-500">No tags yet. Add tags to your notes!</p>
                    @endif
                </div>
            </x-panel>
        </section>
    </main>
</div>
@endsection
