<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class TravelAttachment extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'travel_attachments';

    protected $fillable = [
        'name',
        'path',
        'travel_id',
    ];

    public function getPathAttribute($path){
        return isset($path) ? asset('storage/' . $path) : null;
    }

    public function user(){
        return $this->belongsTo(User::class);
    }
}
