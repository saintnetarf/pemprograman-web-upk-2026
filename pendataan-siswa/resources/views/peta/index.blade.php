@extends('layouts.app')

@section('title', 'Peta Sebaran Siswa')

@push('styles')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
@endpush

@section('content')
<div class="mb-4">
    <h1 class="text-2xl font-semibold">Peta Sebaran Tempat Tinggal Siswa</h1>
    <p class="text-slate-600">
        Menampilkan <strong>{{ $total }}</strong> siswa sesuai hak akses Anda. Klik marker untuk melihat detail.
        @if ($tanpaTitik > 0)
            <span class="text-amber-600 font-medium">{{ $tanpaTitik }} siswa belum memiliki titik koordinat.</span>
        @endif
    </p>
</div>

<div id="map" class="h-[70vh] min-h-[420px] rounded border" style="height: 70vh; min-height: 420px;"></div>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Mencegah teks dari database dibaca sebagai HTML (anti XSS)
            const esc = (s) => String(s ?? '').replace(/[&<>"']/g, (c) => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));

            // 1. Peta dasar OpenStreetMap
            const map = L.map('map').setView([-3.3194, 114.5908], 12);
            L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            // 2. Mengambil data GeoJSON dari backend
            fetch("{{ route('peta.data') }}", {
                headers: {
                    'Accept': 'application/json'
                }
            })
                .then((res) => {
                    if (!res.ok) {
                        throw new Error(`Gagal mengambil data peta (${res.status})`);
                    }

                    return res.json();
                })
                .then((geojson) => {
                    const features = Array.isArray(geojson.features)
                        ? geojson.features.filter((feature) => {
                            const coordinates = feature.geometry?.coordinates;
                            return Array.isArray(coordinates)
                                && coordinates.length >= 2
                                && Number.isFinite(Number(coordinates[0]))
                                && Number.isFinite(Number(coordinates[1]));
                        })
                        : [];

                    // 3. Membuat marker untuk setiap fitur beserta popup
                    const layer = L.geoJSON({ ...geojson, features }, {
                        onEachFeature: (feature, marker) => {
                            const p = feature.properties;
                            const editHtml = p.edit_url
                                ? `<br><a href="${esc(p.edit_url)}" class="text-blue-600 underline">Ubah data</a>`
                                : '';

                            marker.bindPopup(`
                                <strong>${esc(p.nama)}</strong> (${esc(p.kelas)})<br>
                                Kel. ${esc(p.kelurahan)}, Kec. ${esc(p.kecamatan)}<br>
                                Jarak ke sekolah: ${esc(p.jarak)}<br>
                                Transportasi: ${esc(p.transportasi)}<br>
                                Waktu tempuh: ${esc(p.waktu_tempuh)}${editHtml}
                            `);
                        }
                    }).addTo(map);

                    // 4. Zoom otomatis agar semua marker terlihat
                    if (features.length > 0) {
                        map.fitBounds(layer.getBounds().pad(0.1));
                    }
                })
                .catch((error) => {
                    console.error('Data peta gagal dimuat:', error);
                    alert('Data peta gagal dimuat. Silakan coba lagi.');
                });
        });
    </script>
@endpush
