<?php
// app/Models/MetodePembayaran.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class MetodePembayaran extends Model
{
    use HasFactory;

    protected $table = 'metode_pembayaran';
    protected $primaryKey = 'id_metode_pembayaran';
    public $timestamps = false; // Table doesn't have timestamps based on test results

    protected $fillable = [
        'uuid',
        'nama_metode_pembayaran',
        'no_metode_pembayaran',
        'deskripsi_1',
        'deskripsi_2',
        'thumbnail',
        'icon',
        'bahan_poster',
        'ukuran_poster',
        'total_harga_poster'
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = (string) Str::uuid();
            }
        });
    }
}