<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BudgetDetail extends Model
{
    use HasFactory;

    public $table = "budget_details";

    public $fillable = [
        'budget_id',
        'presentation_text_1',
        'presentation_text_2',
        'presentation_text_3',
        'development_text_1',
        'development_text_2',
        'development_text_3',
        'development_text_4',
        'payment_methods',
        'conclusion_text_1',
        'conclusion_text_2',
        'presentation_image_1',
        'presentation_image_2',
        'presentation_image_3',
        'development_image_1',
        'development_image_2',
        'development_image_3',
        'development_image_4',
        'conclusion_image_1',
        'conclusion_image_2',
        'cover',
        'final_cover',
    ];

    public function getCoverAttribute($value)
    {
        return $value ? asset($value) : null;
    }
    
    public function budget(){
        return $this->belongsTo(Budget::class);
    }
}
