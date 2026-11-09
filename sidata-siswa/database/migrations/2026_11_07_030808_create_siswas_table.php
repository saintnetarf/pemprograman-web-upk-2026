<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siswas', function (Blueprint $table) {
            $table->id();

            // Identitas siswa
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('status_dalam_keluarga', 30);
            $table->unsignedTinyInteger('anak_ke');
            $table->unsignedTinyInteger('jumlah_saudara');
            $table->boolean('pernah_tk')->default(false);
            $table->boolean('pernah_paud')->default(false);
            $table->string('hobi', 100);
            $table->string('cita_cita', 100);
            $table->string('no_hp', 20)->default('-');

            // Alamat tinggal sekarang
            $table->text('alamat');
            $table->string('kabupaten_kota', 100);
            $table->string('kecamatan', 100);
            $table->string('kelurahan', 100);
            $table->string('kode_pos', 5);
            $table->string('status_tinggal', 40);

            // Jarak dan transportasi ke sekolah
            $table->string('jarak_ke_sekolah', 30);
            $table->string('transportasi', 30);
            $table->string('waktu_tempuh', 20);

            // Data spasial: titik koordinat rumah
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siswas');
    }
};
