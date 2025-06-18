<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Transaksi extends Model
{
    use HasFactory;
    protected $table = "transaksi";
    protected $primaryKey = "id_transaksi";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    protected $fillable = [
        'order_id', 'jumlah', 'status_transaksi', 'bukti_pembayaran', 'waktu_pembayaran', 'confirmed_at', 'admin_notes', 'alasan_penolakan', 'expired_at', 'id_metode_pembayaran', 'id_pesanan'
    ];

    protected $casts = [
        'waktu_pembayaran' => 'datetime',
        'confirmed_at' => 'datetime',
        'expired_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function toMetodePembayaran()
    {
        return $this->belongsTo(MetodePembayaran::class, 'id_metode_pembayaran');
    }
    public function toPesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
}