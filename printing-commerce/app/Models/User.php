<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class User extends Model
{
    use HasFactory;
    protected $table = "users";
    protected $primaryKey = "id_user";
    public $incrementing = true;
    protected $keyType = 'integer';
    public $timestamps = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'uuid',
        'fcm_token',
        'fcm_token_updated_at',
        'device_id',
        'device_type',
        'nama_user',
        'jenis_kelamin',
        'no_telpon',
        'alamat',
        'no_rekening',
        'email_verified_at',
        'created_at',
        'updated_at',
        'id_auth',
        'foto'
    ];
    public function fromVerifikasi()
    {
        return $this->hasMany(VerifikasiUser::class, 'id_verifikasi_user');
    }
    public function fromPesanan()
    {
        return $this->hasMany(Pesanan::class, 'id_user');
    }
    public function toAuth()
    {
        return $this->belongsTo(Auth::class, 'id_auth');
    }
    public function reviews()
    {
        return $this->hasManyThrough(
            Review::class,
            Pesanan::class,
            'id_user',
            'id_pesanan',
            'id_user',
            'id_pesanan'
        );
    }
    public function getAvatarUrlAttribute()
    {
        return $this->foto;
    }
    /**
     * Update FCM token untuk user ini
     */
    public function updateFcmToken($fcmToken, $deviceId = null, $deviceType = null)
    {
        $this->update([
            'fcm_token' => $fcmToken,
            'fcm_token_updated_at' => now(),
            'device_id' => $deviceId,
            'device_type' => $deviceType,
        ]);
        
        return $this;
    }
    /**
     * Check apakah FCM token masih valid (tidak expired)
     */
    public function isFcmTokenValid()
    {
        if (!$this->fcm_token) {
            return false;
        }
        
        // FCM token dianggap expired kalau lebih dari 30 hari tidak update
        return $this->fcm_token_updated_at && 
               $this->fcm_token_updated_at->diffInDays(now()) <= 30;
    }
}