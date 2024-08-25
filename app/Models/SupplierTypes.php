<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierTypes extends Model
{
    use HasFactory;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'types_supplier';

    protected $fillable = [
        'type'
    ];
}
