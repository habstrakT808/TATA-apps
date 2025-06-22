<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StatistikPesanan extends Model
{
    use HasFactory;

    protected $table = 'statistik_pesanan';
    
    protected $fillable = [
        'id_pesanan',
        'pelanggan',
        'jenis_jasa',
        'total_harga',
        'completed_at'
    ];
    
    protected $casts = [
        'completed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'total_harga' => 'decimal:2'
    ];
    
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
} 