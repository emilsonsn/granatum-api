<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const DELETED_AT = 'deleted_at';

    public $table = 'budgets';

    public $fillable = [
        'title',
        'description',
    ];

    public function budgetGenerateds(){
        return $this->hasMany(BudgetGenerated::class);
    }

    public function budgetDetails(){
        return $this->hasMany(BudgetDetail::class);
    }
}
