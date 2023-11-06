<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;

    public function attemp(){
        return $this->belongsTo(Attempt::class);
    }

    protected $fillable = [
        'attempt_id',
        'question',
        'answer',
        'is_correct'
    ];
    protected $casts = [
        'answer' => 'array'
    ];

}

