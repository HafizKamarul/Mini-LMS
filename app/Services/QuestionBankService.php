<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionBankQuestion;
use App\Models\QuestionOption;

class QuestionBankService
{
    public function syncFromQuestion(Question $question): QuestionBankQuestion
    {
        $question->loadMissing('options');

        $payload = [
            'question_text' => trim((string) $question->question_text),
            'type' => (string) $question->type,
            'marks' => (string) $question->marks,
            'explanation' => $question->explanation,
            'keywords' => collect($question->keywords ?? [])->map(fn ($keyword) => trim((string) $keyword))->filter()->values()->all(),
            'options' => $question->options
                ->sortBy('order')
                ->values()
                ->map(fn (QuestionOption $option): array => [
                    'option_text' => trim((string) $option->option_text),
                    'is_correct' => (bool) $option->is_correct,
                    'order' => (int) $option->order,
                ])
                ->all(),
        ];

        $fingerprint = hash('sha256', json_encode($payload, JSON_UNESCAPED_UNICODE));

        $bankQuestion = QuestionBankQuestion::query()->firstOrCreate(
            ['fingerprint' => $fingerprint],
            [
                'question_text' => $payload['question_text'],
                'type' => $payload['type'],
                'marks' => (float) $question->marks,
                'explanation' => $payload['explanation'],
                'keywords' => $payload['keywords'] === [] ? null : $payload['keywords'],
            ]
        );

        if (! $bankQuestion->options()->exists()) {
            foreach ($payload['options'] as $option) {
                $bankQuestion->options()->create($option);
            }
        }

        if ($question->question_bank_question_id !== $bankQuestion->id) {
            $question->update(['question_bank_question_id' => $bankQuestion->id]);
        }

        return $bankQuestion;
    }

    public function cloneToExam(Exam $exam, QuestionBankQuestion $bankQuestion): Question
    {
        $bankQuestion->loadMissing('options');

        $nextOrder = (int) ($exam->questions()->max('order') ?? 0) + 1;

        $question = Question::query()->create([
            'exam_id' => $exam->id,
            'question_bank_question_id' => $bankQuestion->id,
            'question_text' => $bankQuestion->question_text,
            'type' => $bankQuestion->type,
            'marks' => $bankQuestion->marks,
            'order' => $nextOrder,
            'explanation' => $bankQuestion->explanation,
            'keywords' => $bankQuestion->keywords,
        ]);

        foreach ($bankQuestion->options as $option) {
            $question->options()->create([
                'option_text' => $option->option_text,
                'is_correct' => $option->is_correct,
                'order' => $option->order,
            ]);
        }

        return $question;
    }
}