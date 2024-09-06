<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'services';

    protected $fillable = [
        'name',
        'service_type_id',
    ];

    public function type(){
        return $this->belongsTo(ServiceType::class, 'service_type_id', 'id');
    }

}
