@use('App\Models\Siswa')

<fieldset class="rounded border bg-white p-4">
    <legend class="px-2 font-semibold">Data Pribadi Siswa</legend>
    <div class="grid gap-4 md:grid-cols-2">
        <x-form-input name="nik" label="NIK Siswa (16 digit)"
                      :value="$siswa->nik" maxlength="16" />
        <x-form-input name="nama_lengkap" label="Nama Lengkap (sesuai akta kelahiran)"
                      :value="$siswa->nama_lengkap" />
        <x-form-select name="status_dalam_keluarga" label="Status Siswa dalam Keluarga"
                       :options="Siswa::STATUS_KELUARGA"
                       :value="$siswa->status_dalam_keluarga" />
        <x-form-input name="anak_ke" label="Anak ke-" type="number" min="1"
                      :value="$siswa->anak_ke" />
        <x-form-input name="jumlah_saudara" label="Jumlah Saudara Kandung"
                      type="number" min="0" :value="$siswa->jumlah_saudara" />
        <x-form-input name="no_hp" label='No. HP Siswa (isi "-" jika tidak ada)'
                      :value="$siswa->no_hp" />
        <x-form-yatidak name="pernah_tk" label="Pernah TK/RA" :value="$siswa->pernah_tk" />
        <x-form-yatidak name="pernah_paud" label="Pernah PAUD" :value="$siswa->pernah_paud" />
        <x-form-input name="hobi" label="Hobi" :value="$siswa->hobi" />
        <x-form-input name="cita_cita" label="Cita-cita" :value="$siswa->cita_cita" />
    </div>
</fieldset>

<fieldset class="rounded border bg-white p-4">
    <legend class="px-2 font-semibold">Alamat Tinggal Sekarang</legend>
    <div class="grid gap-4 md:grid-cols-2">
        <div class="md:col-span-2">
            <label for="alamat" class="mb-1 block text-sm font-medium">
                Alamat Lengkap (nomor rumah, RT, dan RW)
            </label>
            <textarea id="alamat" name="alamat" rows="2"
                      class="w-full rounded border border-slate-300 px-3 py-2">{{ old('alamat', $siswa->alamat) }}</textarea>
            @error('alamat')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>
        <x-form-input name="kabupaten_kota" label="Kabupaten/Kota"
                      :value="$siswa->kabupaten_kota ?? 'Kota Banjarmasin'" />
        <x-form-input name="kecamatan" label="Kecamatan" :value="$siswa->kecamatan" />
        <x-form-input name="kelurahan" label="Kelurahan" :value="$siswa->kelurahan" />
        <x-form-input name="kode_pos" label="Kode Pos" maxlength="5"
                      :value="$siswa->kode_pos" />
        <x-form-select name="status_tinggal" label="Status Tempat Tinggal Sekarang"
                       :options="Siswa::STATUS_TINGGAL" :value="$siswa->status_tinggal" />
    </div>
</fieldset>

<fieldset class="rounded border bg-white p-4">
    <legend class="px-2 font-semibold">Jarak dan Transportasi ke Sekolah</legend>
    <div class="grid gap-4 md:grid-cols-3">
        <x-form-select name="jarak_ke_sekolah" label="Jarak Rumah ke Sekolah"
                       :options="Siswa::JARAK" :value="$siswa->jarak_ke_sekolah" />
        <x-form-select name="transportasi" label="Transportasi ke Sekolah"
                       :options="Siswa::TRANSPORTASI" :value="$siswa->transportasi" />
        <x-form-select name="waktu_tempuh" label="Waktu Tempuh ke Sekolah"
                       :options="Siswa::WAKTU_TEMPUH" :value="$siswa->waktu_tempuh" />
    </div>
</fieldset>

<fieldset class="rounded border bg-white p-4">
    <legend class="px-2 font-semibold">Titik Koordinat Rumah</legend>
    <p class="mb-2 text-sm text-slate-600">
        Klik pada peta untuk menandai lokasi rumah. Penanda dapat digeser untuk
        menyesuaikan posisi.
    </p>
    <div id="map-picker" class="h-96 rounded border"></div>

    <div class="mt-3 grid gap-4 md:grid-cols-3">
        <x-form-input name="latitude" label="Latitude (lintang)" type="number"
                      step="any" :value="$siswa->latitude" />
        <x-form-input name="longitude" label="Longitude (bujur)" type="number"
                      step="any" :value="$siswa->longitude" />
        <div class="flex items-end">
            <button type="button" id="btn-lokasi"
                    class="rounded border border-slate-300 bg-white px-4 py-2 hover:bg-slate-100">
                Gunakan lokasi saya
            </button>
        </div>
    </div>
</fieldset>

@if (auth()->user()?->hasRole(\App\Enums\Role::Sekolah))
    <fieldset class="rounded border p-4">
        <legend class="px-2 font-medium">Kelas dan Penautan Akun</legend>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
            <x-form-select-relasi
                name="kelas_id"
                label="Kelas"
                :options="$daftarKelas"
                :value="$siswa->kelas_id"
                placeholder="-- Pilih Kelas --"
            />
            <x-form-select-relasi
                name="user_id"
                label="Akun Siswa"
                :options="$akunSiswa"
                :value="$siswa->user_id"
                placeholder="-- Pilih Akun Siswa --"
            />
            <x-form-select-relasi
                name="orang_tua_id"
                label="Akun Orang Tua"
                :options="$akunOrtu"
                :value="$siswa->orang_tua_id"
                placeholder="-- Pilih Akun Orang Tua --"
            />
        </div>
    </fieldset>
@endif

<div class="flex gap-3">
    <button type="submit"
            class="rounded bg-blue-600 px-5 py-2 text-white hover:bg-blue-700">Simpan</button>
    <a href="{{ route('siswa.index') }}"
       class="rounded border border-slate-300 bg-white px-5 py-2 hover:bg-slate-100">Batal</a>
</div>

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const latInput = document.getElementById('latitude');
            const lngInput = document.getElementById('longitude');
            const pusatDefault = [-3.3194, 114.5908]; // Banjarmasin

            const adaKoordinat = latInput.value !== '' && lngInput.value !== '';
            const awal = adaKoordinat
                ? [parseFloat(latInput.value), parseFloat(lngInput.value)]
                : pusatDefault;

            // 1. Membuat peta dan lapisan dasar OpenStreetMap
            const map = L.map('map-picker').setView(awal, adaKoordinat ? 17 : 12);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 2. Fungsi mengisi input lintang dan bujur
            let marker = null;

            function isiInput(lat, lng) {
                latInput.value = lat.toFixed(7);
                lngInput.value = lng.toFixed(7);
            }

            // 3. Fungsi menaruh atau memindahkan marker
            function taruhMarker(lat, lng) {
                if (marker) {
                    marker.setLatLng([lat, lng]);
                    return;
                }
                marker = L.marker([lat, lng], { draggable: true }).addTo(map);
                marker.on('dragend', () => {
                    const p = marker.getLatLng();
                    isiInput(p.lat, p.lng);
                });
            }

            if (adaKoordinat) {
                taruhMarker(awal[0], awal[1]);
            }

            // 4. Klik pada peta menentukan titik rumah
            map.on('click', (e) => {
                taruhMarker(e.latlng.lat, e.latlng.lng);
                isiInput(e.latlng.lat, e.latlng.lng);
            });

            // 5. Jika koordinat diketik manual, marker mengikuti
            [latInput, lngInput].forEach((el) => {
                el.addEventListener('change', () => {
                    const lat = parseFloat(latInput.value);
                    const lng = parseFloat(lngInput.value);
                    if (!isNaN(lat) && !isNaN(lng)) {
                        taruhMarker(lat, lng);
                        map.setView([lat, lng], 17);
                    }
                });
            });

            // 6. Tombol "Gunakan lokasi saya" (memakai GPS atau jaringan perangkat)
            document.getElementById('btn-lokasi').addEventListener('click', () => {
                if (!navigator.geolocation) {
                    alert('Peramban tidak mendukung geolokasi.');
                    return;
                }
                navigator.geolocation.getCurrentPosition(
                    (pos) => {
                        const { latitude, longitude } = pos.coords;
                        taruhMarker(latitude, longitude);
                        isiInput(latitude, longitude);
                        map.setView([latitude, longitude], 17);
                    },
                    () => alert('Lokasi tidak dapat diambil. Izinkan akses lokasi pada peramban.')
                );
            });
        });
    </script>
@endpush
