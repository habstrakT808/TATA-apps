<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisiEditor extends Model
{
    use HasFactory;
    
    protected $table = 'revisi_editor';
    protected $primaryKey = 'id_revisi_editor';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    
    protected $fillable = [
        'nama_file', 'catatan_editor', 'uploaded_at', 'id_editor', 'id_revisi'
    ];
    
    protected $casts = [
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Relationships
    public function editor()
    {
        return $this->belongsTo(Editor::class, 'id_editor');
    }
    
    public function revision()
    {
        return $this->belongsTo(Revisi::class, 'id_revisi');
    }
    
    // Helper methods
    public function getFileUrlAttribute()
    {
        return asset('uploads/revisi_editor/' . $this->nama_file);
    }
} 