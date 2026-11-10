<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kelas extends Model
{
    protected $table = 'kelas';

    protected $fillable = [
        'nama',
        'wali_kelas_id',
    ];

    public function waliKelas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'wali_kelas_id');
    }

    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }
}
