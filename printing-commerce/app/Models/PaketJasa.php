<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class PaketJasa extends Model
{
    use HasFactory;
    protected $table = "paket_jasa";
    protected $primaryKey = "id_paket_jasa";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'kelas_jasa', 'deskripsi_singkat', 'harga_paket_jasa', 'waktu_pengerjaan', 'maksimal_revisi', 'id_jasa'
    ];
    public function fromPesanan()
    {
        return $this->hasMany(Review::class, 'id_review');
    }
    public function toJasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
    }
}