<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatatanPesanan extends Model
{
    use HasFactory;
    
    protected $table = 'catatan_pesanan';
    protected $primaryKey = 'id_catatan_pesanan';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'catatan_pesanan', 'gambar_referensi', 'id_pesanan', 'id_user'
    ];
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function getGambarUrlAttribute()
    {
        if ($this->gambar_referensi) {
            return asset('uploads/catatan_pesanan/' . $this->gambar_referensi);
        }
        return null;
    }
    public function hasGambar()
    {
        return !empty($this->gambar_referensi);
    }
}