<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Result extends Model
{
    use HasFactory;

    protected $fillable = [
        'submission_id',
        'exam_id',
        'student_id',
        'score',
        'total_marks',
        'percentage',
        'time_taken',
        'passed',
        'published_at',
    ];

    protected function casts(): array
    {
        return [
            'score' => 'decimal:2',
            'total_marks' => 'decimal:2',
            'percentage' => 'decimal:2',
            'time_taken' => 'integer',
            'passed' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function submission(): BelongsTo
    {
        return $this->belongsTo(Submission::class);
    }

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }
}
