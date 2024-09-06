<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';

    public $table = 'suppliers';

    protected $fillable = [
        'fantasy_name',
        'cnpj',
        'phone',
        'whatsapp',
        'email',
        'type_supplier_id',
        'address',
        'city',
        'state',
    ];

    public function supplierType(){
        return $this->belongsTo(SupplierTypes::class, 'type_supplier_id', 'id');
    }
}
