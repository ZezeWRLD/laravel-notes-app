<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('My Notes') }}
            </h2>
            <a href="{{ route('notes.create') }}"
               class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                New Note
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Success Message -->
            @if(session('success'))
                <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif

            <!-- Notes Grid -->
            @if($notes->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($notes as $note)
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg hover:shadow-md transition">
                            <div class="p-6">
                                <div class="flex justify-between items-start mb-3">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        <a href="{{ route('notes.show', $note) }}"
                                           class="hover:text-blue-600 dark:hover:text-blue-400">
                                            {{ $note->title }}
                                        </a>
                                    </h3>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 whitespace-nowrap ml-2">
                                        {{ $note->created_at->format('M d') }}
                                    </div>
                                </div>

                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-4 line-clamp-3">
                                    {{ $note->content }}
                                </p>

                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 dark:text-gray-400">
                                        {{ $note->created_at->diffForHumans() }}
                                    </span>
                                    <div class="flex space-x-2">
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
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination (if using paginate() instead of get()) -->
                @if($notes instanceof \Illuminate\Pagination\AbstractPaginator && $notes->hasPages())
                    <div class="mt-8">
                        {{ $notes->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <div class="mb-4">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <p class="text-gray-500 dark:text-gray-400 mb-4">
                        No notes found. Create your first note to get started!
                    </p>
                    <a href="{{ route('notes.create') }}"
                       class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 active:bg-blue-800 focus:outline-none focus:border-blue-900 focus:ring ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150">
                        Create Your First Note
                    </a>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
