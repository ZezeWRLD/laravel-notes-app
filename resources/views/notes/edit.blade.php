@extends('layouts.app')

@section('content')
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Note') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    <!-- Back Button -->
                    <div class="mb-6">
                        <a href="{{ route('notes.show', $note) }}"
                           class="inline-flex items-center text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Back to Note
                        </a>
                    </div>

                    <!-- Edit Form -->
                    <form action="{{ route('notes.update', $note) }}" method="POST">
    @csrf
    @method('PUT') <!-- IMPORTANT: This makes it a PUT request -->

    <div class="space-y-6">
        <!-- Title Field -->
        <div>
            <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Title *
            </label>
            <input type="text"
                   name="title"
                   id="title"
                   value="{{ old('title', $note->title) }}"
                   required
                   maxlength="255"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('title') border-red-500 @enderror"
                   placeholder="Enter note title">

            @error('title')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Content Field -->
        <div>
            <label for="content" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Content
            </label>
            <textarea name="content"
                      id="content"
                      rows="10"
                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white @error('content') border-red-500 @enderror"
                      placeholder="Write your note content here...">{{ old('content', $note->content) }}</textarea>

            @error('content')
                <p class="mt-2 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
            @enderror
        </div>

        <!-- Tags Field -->
        <div>
            <label for="tags" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                Tags
            </label>
            <input type="text"
                   name="tags"
                   id="tags"
                   value="{{ old('tags', $note->tags->pluck('name')->implode(', ')) }}"
                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                   placeholder="Add tags separated by commas (e.g., work, important, urgent)">

            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                Separate multiple tags with commas
            </p>
        </div>

        <!-- Submit Button -->
        <div class="flex items-center justify-end space-x-4 pt-6 border-t dark:border-gray-700">
            <button type="submit"
                    class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                Update Note
            </button>
        </div>
    </div>
</form>

                </div>
            </div>
        </div>
    </div>
@endsection
