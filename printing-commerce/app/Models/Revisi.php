<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Revisi extends Model
{
    use HasFactory;
    
    protected $table = 'revisi';
    protected $primaryKey = 'id_revisi';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    
    protected $fillable = [
        'urutan_revisi',
        'catatan_user',
        'id_pesanan'
    ];
    
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Relationships
    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan');
    }
    
    public function userFiles()
    {
        return $this->hasMany(RevisiUser::class, 'id_revisi');
    }
    
    public function editorFiles()
    {
        return $this->hasMany(RevisiEditor::class, 'id_revisi');
    }
    
    // Helper methods
    public function hasUserFiles()
    {
        return $this->userFiles()->count() > 0;
    }
    
    public function hasEditorResponse()
    {
        return $this->editorFiles()->count() > 0;
    }
    
    public function getStatusAttribute()
    {
        if (!$this->hasUserFiles()) {
            return 'pending_user_files';
        }
        
        if (!$this->hasEditorResponse()) {
            return 'waiting_editor_response';
        }
        
        return 'selesai';
    }
} 