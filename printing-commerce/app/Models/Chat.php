<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Chat extends Model
{
    use HasFactory;

    protected $connection = 'mysql';
    protected $table = 'chats';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'uuid',
        'user_id',
        'admin_id',
        'pesanan_uuid',
        'last_message',
        'unread_count',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id_admin');
    }

    public function pesanan()
    {
        return $this->belongsTo(Pesanan::class, 'pesanan_uuid', 'uuid');
    }

    public function messages()
    {
        return $this->hasMany(ChatMessage::class, 'chat_uuid', 'uuid');
    }
} 