@props(['type' => 'submit'])

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => 'group relative w-full flex justify-center py-2 px-4 border border-transparent
                                       text-sm font-medium rounded-md text-white bg-note-primary hover:bg-note-accent
                                       focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-note-primary']) }}
>
    {{ $slot }}
</button>
