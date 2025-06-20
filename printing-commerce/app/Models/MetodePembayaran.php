<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class MetodePembayaran extends Model
{
    use HasFactory;
    protected $table = "metode_pembayaran";
    protected $primaryKey = "id_metode_pembayaran";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'uuid', 
        'nama_metode_pembayaran', 
        'no_metode_pembayaran', 
        'deskripsi_1', 
        'deskripsi_2', 
        'harga_jasa',
        'harga_logo',
        'harga_poster',
        'harga_banner',
        'bahan_banner',
        'ukuran',
        'total_harga',
        'bahan_poster',
        'ukuran_poster',
        'total_harga_poster'
    ];
    
    protected $appends = [
        'harga_jasa',
        'harga_logo',
        'harga_poster',
        'harga_banner',
        'bahan_banner',
        'ukuran',
        'total_harga',
        'bahan_poster',
        'ukuran_poster',
        'total_harga_poster'
    ];
    
    public function getHargaJasaAttribute()
    {
        return $this->attributes['harga_jasa'] ?? 'Regular';
    }
    
    public function getHargaLogoAttribute()
    {
        return $this->attributes['harga_logo'] ?? '150.000';
    }
    
    public function getHargaPosterAttribute()
    {
        return $this->attributes['harga_poster'] ?? '150.000';
    }
    
    public function getHargaBannerAttribute()
    {
        return $this->attributes['harga_banner'] ?? '150.000';
    }
    
    public function getBahanBannerAttribute()
    {
        return $this->attributes['bahan_banner'] ?? 'Flexi China';
    }
    
    public function getUkuranAttribute()
    {
        return $this->attributes['ukuran'] ?? '1 x 2 m';
    }
    
    public function getTotalHargaAttribute()
    {
        return $this->attributes['total_harga'] ?? '200.000';
    }
    
    public function getBahanPosterAttribute()
    {
        return $this->attributes['bahan_poster'] ?? 'Art Paper';
    }
    
    public function getUkuranPosterAttribute()
    {
        return $this->attributes['ukuran_poster'] ?? 'A3';
    }
    
    public function getTotalHargaPosterAttribute()
    {
        return $this->attributes['total_harga_poster'] ?? '150.000';
    }
    
    public function fromTransaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_transaksi');
    }
}