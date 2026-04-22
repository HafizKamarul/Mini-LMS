<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Question extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id',
        'question_bank_question_id',
        'question_text',
        'type',
        'marks',
        'order',
        'explanation',
        'keywords',
    ];

    protected function casts(): array
    {
        return [
            'marks' => 'decimal:2',
            'keywords' => 'array',
        ];
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class);
    }

    public function bankQuestion(): BelongsTo
    {
        return $this->belongsTo(QuestionBankQuestion::class, 'question_bank_question_id');
    }

    public function answers(): HasMany
    {
        return $this->hasMany(Answer::class);
    }
}
