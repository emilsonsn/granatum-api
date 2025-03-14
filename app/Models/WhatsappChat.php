<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class WhatsappChat extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'whatsapp_chats';

    protected $appends = ['unread_count'];

    protected $fillable = [
        'name',
        'remoteJid',
        'instance',
        'instanceId',
        'profilePicUrl',
        'apiKey',
        'status',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function messages(){
        return $this->hasMany(ChatMessage::class);
    }

    public function lastMessage()
    {
        return $this->hasOne(ChatMessage::class, 'whatsapp_chat_id','id')->latestOfMany();
    }

    public function getUnreadCountAttribute()
    {
        return $this->messages()
            ->where('read', false)
            ->where('fromMe', false)
            ->count();
    }
}
