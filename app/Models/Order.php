<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'orders';

    protected $fillable = [
        'order_type',
        'date',
        'construction_id',
        'user_id',
        'supplier_id',
        'quantity_items',
        'description',
        'total_value',
        'payment_method',
        'purchase_status',
        'purchase_date',
        'has_granatum',
        'bank_id',
        'category_id',
        'tag_id',
        'external_suplier_id',
        'cost_center_id'
    ];

    public function construction(){
        return $this->belongsTo(Construction::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function supplier(){
        return $this->belongsTo(Supplier::class);
    }

    public function files(){
        return $this->hasMany(OrderFile::class);
    }

    public function items(){
        return $this->hasMany(Item::class);
    }

    public function releases(){
        return $this->hasMany(Release::class);
    }

    public function solicitation(){
        return $this->hasOne(Solicitation::class);
    }


}
