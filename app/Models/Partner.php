<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Partner extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'partners';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'cnpj_cpf',
        'activity',
        'image',
        'is_active',
    ];

    public function getImageAtributte($value){
        return $value ? asset("storage/$value") : '';
    }
}
