<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Review extends Model
{
    use HasFactory;
    protected $table = "review";
    protected $primaryKey = "id_review";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = false;
    protected $fillable = [
        'review', 'rating', 'created_at', 'id_pesanan'
    ];
    public function toPesanan()
    {
        return $this->belongsTo(Pesanan::class, 'id_pesanan', 'id_pesanan');
    }
    // Relasi langsung ke user melalui pesanan
    public function user()
    {
        return $this->hasOneThrough(
            User::class,
            Pesanan::class,
            'id_pesanan', // Foreign key di pesanan (local)
            'id_user',    // Foreign key di users (local)
            'id_pesanan', // Local key di review
            'id_user'     // Local key di pesanan
        );
    }
    // Accessor untuk mendapatkan data user
    public function getUserDataAttribute()
    {
        return $this->toPesanan->toUser ?? null;
    }
}