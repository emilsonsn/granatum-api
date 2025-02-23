<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetGenerated extends Model
{
    use HasFactory;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'budget_generateds';

    public $fillable = [
        'description',
        'budget_id',
        'lead_id',
        'status',
    ];

    public function budget(){
        return $this->belongsTo(Budget::class);
    }

    public function lead(){
        return $this->belongsTo(Lead::class);
    }

    public function model(){
        return $this->belongsTo(Budget::class);
    }
}
