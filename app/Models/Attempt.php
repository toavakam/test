<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attempt extends Model
{
    use HasFactory;

    public static function where(string $string, int $pk)
    {
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id', 'id');
    }

    public function result()
    {
        return $this->hasMany(Result::class, 'attempt_id', 'id');
    }

    protected $fillable = [
        'name',
        'lastname',
        'completed',
        'question_count',
        'correct_answer_count',
        'test_id',
        'QuestionOrder',
    ];

    protected $casts = [
        'QuestionOrder' => 'array',
    ];
}
