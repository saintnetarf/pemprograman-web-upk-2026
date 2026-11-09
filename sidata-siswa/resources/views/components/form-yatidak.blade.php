@props(['name', 'label', 'value' => null])

@php
    $current = old($name, is_null($value) ? null : (int) $value);
@endphp

<div>
    <span class="mb-1 block text-sm font-medium">{{ $label }}</span>
    <label class="mr-4">
        <input type="radio" name="{{ $name }}" value="1" @checked((string) $current === '1')> Ya
    </label>
    <label>
        <input type="radio" name="{{ $name }}" value="0" @checked((string) $current === '0')> Tidak
    </label>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
