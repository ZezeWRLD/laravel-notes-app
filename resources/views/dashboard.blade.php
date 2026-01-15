@extends('layouts.app')

@section('content')
    <div class="py-12">
        <header class="mb-8">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Dashboard') }}
            </h2>
        </header>
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Welcome Card -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mb-6">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-2">Welcome back, {{ Auth::user()->name }}!</h3>
                    <p>You have {{ $notes->count() }} {{ Str::plural('note', $notes->count()) }}.</p>

                    <div class="mt-4">
                        <a href="{{ route('notes.create') }}"
                           class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                            Create New Note
                        </a>
                        <a href="{{ route('notes.index') }}"
                           class="ml-3 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-800 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                            View All Notes
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Notes -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <h3 class="text-lg font-semibold mb-4">Recent Notes</h3>

                    @if($notes->count() > 0)
                        <div class="space-y-4">
                            @foreach($notes->take(5) as $note)
                                <div class="border-l-4 border-blue-500 pl-4 py-2 hover:bg-gray-50 dark:hover:bg-gray-700 transition">
                                    <div class="flex justify-between items-start">
                                        <div class="flex-1">
                                            <h4 class="font-medium">
                                                <a href="{{ route('notes.show', $note) }}"
                                                   class="text-gray-900 dark:text-gray-100 hover:text-blue-600 dark:hover:text-blue-400">
                                                    {{ $note->title }}
                                                </a>
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                                                {{ Str::limit($note->content, 100) }}
                                            </p>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400 ml-4 whitespace-nowrap">
                                            {{ $note->created_at->format('M d, Y') }}
                                        </div>
                                    </div>
                                    <div class="mt-2 text-xs flex space-x-3">
                                        <a href="{{ route('notes.edit', $note) }}"
                                           class="text-blue-600 hover:text-blue-800 dark:text-blue-400">
                                            Edit
                                        </a>
                                        <form action="{{ route('notes.destroy', $note) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="text-red-600 hover:text-red-800 dark:text-red-400"
                                                    onclick="return confirm('Are you sure you want to delete this note?')">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            @endforeach

                            @if($notes->count() > 5)
                                <div class="pt-4 border-t dark:border-gray-700">
                                    <a href="{{ route('notes.index') }}"
                                       class="text-blue-600 dark:text-blue-400 hover:underline">
                                        View all {{ $notes->count() }} notes →
                                    </a>
                                </div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-8">
                            <p class="text-gray-500 dark:text-gray-400 mb-4">
                                You haven't created any notes yet.
                            </p>
                            <a href="{{ route('notes.create') }}"
                               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Create Your First Note
                            </a>
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
@endsection
