<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Jasa extends Model
{
    use HasFactory;
    protected $table = "jasa";
    protected $primaryKey = "id_jasa";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'uuid',
        'kategori',
        'deskripsi_jasa',
        'created_at',
        'updated_at'
    ];
    public function fromPaketJasa()
    {
        return $this->hasMany(PaketJasa::class, 'id_paket_jasa');
    }
    
    public function images()
    {
        return $this->hasMany(JasaImage::class, 'id_jasa', 'id_jasa');
    }
}