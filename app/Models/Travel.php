<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Travel extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'travels';

    protected $fillable = [
        'description',
        'type',
        'transport',
        'purchase_date',
        'total_value',
        'has_granatum',
        'purchase_status',
        'observations',
        'bank_id',
        'category_id',
        'tag_id',
        'external_suplier_id',
        'cost_center_id',
        'solicitation_type',
        'user_id',
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function files(){
        return $this->hasMany(TravelAttachment::class);
    }

    public function releases(){
        return $this->hasMany(Release::class);
    }
}
