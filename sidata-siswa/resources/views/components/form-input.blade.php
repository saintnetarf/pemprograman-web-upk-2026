@props(['name', 'label', 'value' => null, 'type' => 'text'])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <input type="{{ $type }}" id="{{ $name }}" name="{{ $name }}"
           value="{{ old($name, $value) }}"
           {{ $attributes->merge(['class' => 'w-full rounded border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none']) }}>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
