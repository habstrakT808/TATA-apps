<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Pesanan extends Model
{
    use HasFactory;
    protected $table = "pesanan";
    protected $primaryKey = "id_pesanan";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    protected $fillable = [
        'uuid', 'deskripsi', 'status_pesanan', 'status_pengerjaan', 'total_harga', 
        'estimasi_waktu', 'estimasi_mulai', 'estimasi_selesai', 'file_hasil_desain',
        'maksimal_revisi', 'confirmed_at', 'assigned_at', 'completed_at', 'client_confirmed_at',
        'id_user', 'id_jasa', 'id_paket_jasa', 'id_editor'
    ];
    protected $casts = [
        'estimasi_waktu' => 'datetime',
        'estimasi_mulai' => 'date',
        'estimasi_selesai' => 'date',
        'confirmed_at' => 'datetime',
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'client_confirmed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    public function userFiles()
    {
        return $this->hasManyThrough(RevisiUser::class, Revisi::class, 'id_pesanan', 'id_revisi');
    }
    
    // Get all editor files through revisions
    public function editorFiles()
    {
        return $this->hasManyThrough(RevisiEditor::class, Revisi::class, 'id_pesanan', 'id_revisi');
    }
    public function fromCatatanPesanan()
    {
        return $this->hasOne(CatatanPesanan::class, 'id_pesanan');
    }
    public function fromTransaksi()
    {
        return $this->hasMany(Transaksi::class, 'id_pesanan');
    }
    public function fromReview()
    {
        return $this->hasMany(Review::class, 'id_review');
    }
    public function toUser()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    public function toJasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa');
    }
    public function toPaketJasa()
    {
        return $this->belongsTo(PaketJasa::class, 'id_paket_jasa');
    }
    
    public function toEditor()
    {
        return $this->belongsTo(Editor::class, 'id_editor');
    }
    
    public function revisions()
    {
        return $this->hasMany(Revisi::class, 'id_pesanan')
            ->orderBy('urutan_revisi', 'asc');
    }
    
    public function latestRevision()
    {
        return $this->hasOne(Revisi::class, 'id_pesanan')
            ->latest('urutan_revisi');
    }
    
    // Dynamic count - no more revisi_used field needed!
    public function getRevisiUsedAttribute()
    {
        return $this->revisions()->count();
    }
    
    // Dynamic calculation
    public function getRevisiTersisaAttribute()
    {
        return $this->maksimal_revisi - $this->revisi_used;
    }
    
    // Get editors who worked on this pesanan
    public function getEditorsAttribute()
    {
        return $this->editorFiles()->with('editor')->get()->pluck('editor')->unique('id_editor');
    }
}