@props(['name', 'label', 'options', 'value' => null, 'placeholder' => '-- Pilih --'])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium text-slate-700">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}"
            {{ $attributes->merge(['class' => 'w-full rounded border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none']) }}>
        <option value="">{{ $placeholder }}</option>
        @foreach ($options as $id => $teks)
            <option value="{{ $id }}" @selected((string) old($name, $value) === (string) $id)>
                {{ $teks }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>

