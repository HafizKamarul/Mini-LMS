<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Answer extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'question_id',
        'question_option_id',
        'answer_text',
        'is_flagged',
        'is_correct',
        'marks_awarded',
    ];

    protected function casts(): array
    {
        return [
            'is_flagged' => 'boolean',
            'is_correct' => 'boolean',
            'marks_awarded' => 'decimal:2',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(Question::class);
    }

    public function option(): BelongsTo
    {
        return $this->belongsTo(QuestionOption::class, 'question_option_id');
    }
}
