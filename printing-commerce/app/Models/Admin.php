<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Admin extends Model
{
    use HasFactory;
    protected $table = "admin";
    protected $primaryKey = "id_admin";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'uuid', 'nama_admin', 'no_telpon', 'id_auth'
    ];
    public function fromVerifikasiAdmin()
    {
        return $this->hasMany(VerifikasiAdmin::class, 'id_verifikasi_admin');
    }
    public function fromCatatanPesanan()
    {
        return $this->hasMany(CatatanPesanan::class, 'id_catatan_pesanan');
    }
    public function toAuth()
    {
        return $this->belongsTo(Auth::class, 'id_auth');
    }
    public function auth()
    {
        return $this->belongsTo(Auth::class, 'id_auth', 'id_auth');
    }
    public function chats()
    {
        return $this->hasMany(Chat::class, 'admin_id', 'id_admin');
    }
}