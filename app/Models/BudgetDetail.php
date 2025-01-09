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

    public $appends = [
        'presentation_image_1_url',
        'presentation_image_2_url',
        'presentation_image_3_url',
        'development_image_1_url',
        'development_image_2_url',
        'development_image_3_url',
        'development_image_4_url',
        'conclusion_image_1_url',
        'conclusion_image_2_url',
    ];

    public function getPresentationImage1UrlAttribute()
    {
        return $this->presentation_image_1 ? asset($this->presentation_image_1) : null;
    }

    public function getPresentationImage2UrlAttribute()
    {
        return $this->presentation_image_2 ? asset($this->presentation_image_2) : null;
    }

    public function getPresentationImage3UrlAttribute()
    {
        return $this->presentation_image_3 ? asset($this->presentation_image_3) : null;
    }

    public function getDevelopmentImage1UrlAttribute()
    {
        return $this->development_image_1 ? asset($this->development_image_1) : null;
    }

    public function getDevelopmentImage2UrlAttribute()
    {
        return $this->development_image_2 ? asset($this->development_image_2) : null;
    }

    public function getDevelopmentImage3UrlAttribute()
    {
        return $this->development_image_3 ? asset($this->development_image_3) : null;
    }

    public function getDevelopmentImage4UrlAttribute()
    {
        return $this->development_image_4 ? asset($this->development_image_4) : null;
    }

    public function getConclusionImage1UrlAttribute()
    {
        return $this->conclusion_image_1 ? asset($this->conclusion_image_1) : null;
    }

    public function getConclusionImage2UrlAttribute()
    {
        return $this->conclusion_image_2 ? asset($this->conclusion_image_2) : null;
    }


    public function getCoverAttribute($value)
    {
        return $value ? asset($value) : null;
    }
    public function getFinalCoverAttribute($value)
    {
        return $value ? asset($value) : null;
    }
    
    public function budget(){
        return $this->belongsTo(Budget::class);
    }
}
