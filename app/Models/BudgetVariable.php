<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetVariable extends Model
{
    use HasFactory;
    
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'budget_variables';

    public $fillable = [
        'key',
        'value',
        'budget_genreated_id',        
    ];

    public function budgetGenreated(){
        return $this->belongsTo(BudgetGenerated::class);
    }
}
