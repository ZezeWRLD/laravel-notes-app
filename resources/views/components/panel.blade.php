{{-- Reusable panel wrapper --}}
<div {{ $attributes->merge(['class' => 'bg-white rounded-xl shadow-md p-6']) }}>
    {{ $slot }}
</div>
