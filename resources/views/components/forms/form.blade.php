@props(['method' => 'POST'])

<form
    method="{{ $method === 'GET' ? 'GET' : 'POST' }}"
    {{ $attributes }}
>
    @unless(in_array($method, ['GET', 'POST']))
        @method($method)
    @endunless

    @unless($method === 'GET')
        @csrf
    @endunless

    {{ $slot }}
</form>
