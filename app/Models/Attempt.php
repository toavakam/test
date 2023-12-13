<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property array $QuestionOrder
 */
class Attempt extends Model
{
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

    public function test(): BelongsTo
    {
        return $this->belongsTo(Test::class);
    }

    public function result(): HasMany
    {
        return $this->hasMany(Result::class);
    }
}
