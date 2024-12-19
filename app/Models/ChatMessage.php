<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ChatMessage extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'chat_messages';

    protected $fillable = [
        'remoteJid',
        'externalId',
        'instanceId',
        'fromMe',
        'message',
        'messageReplied',
        'read',
        'type',
        'path',
        'whatsapp_chat_id',
    ];

    public function chat(){
        return $this->belongsTo(WhatsappChat::class);
    }

    public function getPathAttribute($value)
    {
        return isset($value) ? asset('storage/' . $value) : null;
    }
}
