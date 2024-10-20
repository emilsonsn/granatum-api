<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FunnelStep extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'funnel_steps';

    protected $fillable = [
        'name',
        'description',
        'funnel_id'
    ];
    
    public function funnel(){
        return $this->belongsTo(Funnel::class);
    }

    public function leads(){
        return $this->hasMany(Lead::class);
    }
}
