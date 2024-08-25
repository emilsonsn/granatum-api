<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderFile extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'order_files';

    protected $fillable = [
        'name',
        'path',
        'order_id',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
}
