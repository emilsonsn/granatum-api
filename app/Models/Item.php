<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'items';

    protected $fillable = [
        'order_id',
        'name',
        'quantity',
        'unit_value',
    ];

    public function order(){
        return $this->belongsTo(Order::class);
    }
 
}
