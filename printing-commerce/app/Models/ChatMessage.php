<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatMessage extends Model
{
    use HasFactory;
    
    protected $table = 'chat_messages';
    protected $primaryKey = 'id';
    public $timestamps = true;
    
    protected $fillable = [
        'uuid',
        'chat_uuid',
        'sender_id',
        'sender_type', // 'user' atau 'admin'
        'message',
        'message_type', // 'text', 'image', 'file'
        'file_url',
        'is_read',
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
    
    public function chat()
    {
        return $this->belongsTo(Chat::class, 'chat_uuid', 'uuid');
    }
    
    public function user()
    {
        if ($this->sender_type === 'user') {
            return $this->belongsTo(User::class, 'sender_id', 'id_auth');
        }
        return null;
    }
    
    public function admin()
    {
        if ($this->sender_type === 'admin') {
            return $this->belongsTo(Admin::class, 'sender_id', 'id_auth');
        }
        return null;
    }
    
    public function getSenderAttribute()
    {
        if ($this->sender_type === 'user') {
            $user = User::where('id_auth', $this->sender_id)->first();
            return $user ? [
                'id' => $user->id_user,
                'name' => $user->nama_user,
                'type' => 'user'
            ] : null;
        } elseif ($this->sender_type === 'admin') {
            $admin = Admin::where('id_auth', $this->sender_id)->first();
            return $admin ? [
                'id' => $admin->id_admin,
                'name' => $admin->nama_admin,
                'type' => 'admin'
            ] : null;
        }
        return null;
    }
} 