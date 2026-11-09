@props(['name', 'label', 'options', 'value' => null])

<div>
    <label for="{{ $name }}" class="mb-1 block text-sm font-medium">{{ $label }}</label>
    <select id="{{ $name }}" name="{{ $name }}"
            class="w-full rounded border border-slate-300 px-3 py-2 focus:border-blue-500 focus:outline-none">
        <option value="">-- Pilih --</option>
        @foreach ($options as $option)
            <option value="{{ $option }}" @selected(old($name, $value) === $option)>
                {{ $option }}
            </option>
        @endforeach
    </select>
    @error($name)
        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
    @enderror
</div>
