<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RevisiUser extends Model
{
    use HasFactory;
    
    protected $table = 'revisi_user';
    protected $primaryKey = 'id_revisi_user';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    
    protected $fillable = [
        'nama_file', 'catatan_user', 'uploaded_at', 'id_revisi', 'id_user'
    ];
    
    protected $casts = [
        'uploaded_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];
    
    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }
    
    public function revision()
    {
        return $this->belongsTo(Revisi::class, 'id_revisi');
    }
    
    // Helper methods
    public function getFileUrlAttribute()
    {
        return asset('uploads/revisi_user/' . $this->nama_file);
    }
} 