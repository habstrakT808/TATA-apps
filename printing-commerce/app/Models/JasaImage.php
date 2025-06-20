<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JasaImage extends Model
{
    use HasFactory;
    
    protected $connection = 'mysql';
    protected $table = 'jasa_images';
    protected $primaryKey = 'id_jasa_image';
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'image_path',
        'id_jasa',
    ];
    public function jasa()
    {
        return $this->belongsTo(Jasa::class, 'id_jasa', 'id_jasa');
    }
} 