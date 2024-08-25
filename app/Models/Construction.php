<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Construction extends Model
{
    use HasFactory;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'services';

    protected $fillable = [
        'name',
        'local',
        'contractor_id',
        'client_id',
        'cno',
        'description',
    ];

    public function contractor(){
        return $this->belongsTo(Client::class, 'contractor_id', 'id');
    }

    public function client(){
        return $this->belongsTo(Client::class, 'client_id', 'id');
    }
}
