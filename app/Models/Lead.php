<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'leads';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'origin',
        'observations',
        'responsible_id',
        'funnel_id',
    ];

    public function responsible(){
        return $this->belongsTo(User::class);
    }

    public function steps(){
        return $this->hasMany(LeadStep::class);
    }

    public function funnel(){
        return $this->belongsTo(Funnel::class);
    }

}
