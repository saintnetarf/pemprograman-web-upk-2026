<div class="space-y-4">
    <x-form-input
        name="name"
        label="Nama"
        :value="$user->name"
        required
    />

    <x-form-input
        name="email"
        label="Email"
        type="email"
        :value="$user->email"
        required
    />

    <x-form-select-relasi
        name="role"
        label="Peran"
        :options="$roles"
        :value="$user->role?->value"
        required
    />

    <x-form-input
        name="password"
        label="{{ $user->exists ? 'Password (kosongkan jika tidak diubah)' : 'Password' }}"
        type="password"
        :required="!$user->exists"
    />

    <x-form-input
        name="password_confirmation"
        label="Konfirmasi Password"
        type="password"
        :required="!$user->exists"
    />
</div>

<div class="mt-6 flex items-center gap-3">
    <button type="submit" class="rounded bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700">
        Simpan
    </button>
    <a href="{{ route('users.index') }}" class="rounded border border-slate-300 px-4 py-2 text-slate-700 hover:bg-slate-100">
        Batal
    </a>
</div>

