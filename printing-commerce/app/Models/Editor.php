<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Editor extends Model
{
    use HasFactory;
    protected $table = "editor";
    protected $primaryKey = "id_editor";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'uuid', 'nama_editor', 'email', 'jenis_kelamin', 'no_telpon'
    ];
    public function revisiFiles()
    {
        return $this->hasMany(RevisiEditor::class, 'id_editor');
    }
    
    // Current assigned pesanan
    public function currentPesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_editor')
            ->whereIn('status_pesanan', ['dikerjakan', 'revisi']);
    }
    
    // All pesanan ever assigned to this editor
    public function allPesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_editor');
    }
}