<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    protected $casts = [
        'lv' => 'array',
        'ru' => 'array',
        'eng' => 'array',
    ];

    public function attemps()
    {
        return $this->hasMany(Attempt::class, 'test_id', 'id');
    }

    public function getQuestions(string $lang): ?array
    {
        return match ($lang) {
            'lv' => $this->lv['questions'],
            'ru' => $this->ru['questions'],
            'en' => $this->eng['questions'],
            default => null,
        };
    }

    public function getQuestionsTitle(string $lang): ?string
    {
        return match ($lang) {
            'lv' => $this->lv['title'],
            'ru' => $this->ru['title'],
            'en' => $this->eng['title'],
            default => null,
        };
    }
}
