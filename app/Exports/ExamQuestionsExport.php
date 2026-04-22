<?php

namespace App\Exports;

use App\Models\Exam;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExamQuestionsExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    public function __construct(private readonly Exam $exam)
    {
    }

    public function headings(): array
    {
        return [
            'question',
            'type',
            'option1',
            'option2',
            'option3',
            'option4',
            'correct_answer',
            'keywords',
        ];
    }

    public function collection()
    {
        return $this->exam->questions()
            ->with('options')
            ->orderBy('order')
            ->orderBy('id')
            ->get()
            ->map(function ($question) {
                $options = $question->options
                    ->sortBy('order')
                    ->values();

                $optionValues = [
                    $options[0]->option_text ?? null,
                    $options[1]->option_text ?? null,
                    $options[2]->option_text ?? null,
                    $options[3]->option_text ?? null,
                ];

                $correctAnswer = null;
                $correctOptionIndex = $options->search(fn ($option) => (bool) $option->is_correct);

                if ($correctOptionIndex !== false) {
                    $correctAnswer = (int) $correctOptionIndex + 1;
                }

                return [
                    'question' => $question->question_text,
                    'type' => $question->type === 'short_answer' ? 'subjective' : 'mcq',
                    'option1' => $optionValues[0],
                    'option2' => $optionValues[1],
                    'option3' => $optionValues[2],
                    'option4' => $optionValues[3],
                    'correct_answer' => $correctAnswer,
                    'keywords' => $question->type === 'short_answer'
                        ? implode(', ', $question->keywords ?? [])
                        : null,
                ];
            });
    }
}
