<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class QuestionBankQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'question_text',
        'type',
        'marks',
        'explanation',
        'keywords',
        'fingerprint',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'keywords' => 'array',
        ];
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionBankOption::class)->orderBy('order')->orderBy('id');
    }
}