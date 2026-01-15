@props(['label', 'name', 'type' => 'text', 'value' => ''])

<div class="space-y-1">
    @if($label)
        <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        {{ $attributes->merge(['class' => 'appearance-none relative block w-full px-3 py-2 border
                                          ' . ($errors->has($name) ? 'border-red-500' : 'border-gray-300') . '
                                          placeholder-gray-500 text-gray-900 rounded-md focus:outline-none
                                          focus:ring-note-primary focus:border-note-primary focus:z-10 sm:text-sm']) }}
    >

    @error($name)
        <p class="text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
