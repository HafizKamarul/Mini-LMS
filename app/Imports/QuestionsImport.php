<?php

namespace App\Imports;

use App\Models\Exam;
use App\Models\Question;
use App\Models\QuestionOption;
use App\Services\QuestionBankService;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuestionsImport implements ToCollection, WithHeadingRow
{
    public int $imported = 0;

    /** @var array<int, string> */
    public array $errors = [];

    public function __construct(private readonly Exam $exam)
    {
    }

    public function collection(Collection $rows): void
    {
        $nextOrder = (int) ($this->exam->questions()->max('order') ?? 0);

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2;

            $questionText = trim((string) ($row['question'] ?? ''));
            $type = strtolower(trim((string) ($row['type'] ?? '')));

            if ($questionText === '') {
                $this->errors[] = "Row {$rowNumber}: question is required.";
                continue;
            }

            if (! in_array($type, ['mcq', 'subjective'], true)) {
                $this->errors[] = "Row {$rowNumber}: type must be mcq or subjective.";
                continue;
            }

            $nextOrder++;

            if ($type === 'mcq') {
                $options = [
                    trim((string) ($row['option1'] ?? '')),
                    trim((string) ($row['option2'] ?? '')),
                    trim((string) ($row['option3'] ?? '')),
                    trim((string) ($row['option4'] ?? '')),
                ];

                if (collect($options)->contains(fn (string $option) => $option === '')) {
                    $this->errors[] = "Row {$rowNumber}: all option1-option4 values are required for mcq.";
                    continue;
                }

                $correctAnswerRaw = trim((string) ($row['correct_answer'] ?? ''));
                $correctIndex = $this->resolveCorrectOptionIndex($correctAnswerRaw, $options);

                if ($correctIndex === null) {
                    $this->errors[] = "Row {$rowNumber}: correct_answer must be 1-4 or match one of option1-option4.";
                    continue;
                }

                $question = Question::query()->create([
                    'exam_id' => $this->exam->id,
                    'question_text' => $questionText,
                    'type' => 'single_choice',
                    'marks' => 1,
                    'order' => $nextOrder,
                    'keywords' => null,
                ]);

                foreach ($options as $optionOrder => $optionText) {
                    QuestionOption::query()->create([
                        'question_id' => $question->id,
                        'option_text' => $optionText,
                        'is_correct' => $correctIndex === $optionOrder,
                        'order' => $optionOrder + 1,
                    ]);
                }

                $question->load('options');
                app(QuestionBankService::class)->syncFromQuestion($question);

                $this->imported++;
                continue;
            }

            $keywordsRaw = trim((string) ($row['keywords'] ?? ''));
            $keywords = collect(explode(',', $keywordsRaw))
                ->map(fn (string $keyword) => trim($keyword))
                ->filter()
                ->unique()
                ->values()
                ->all();

            if ($keywords === []) {
                $this->errors[] = "Row {$rowNumber}: keywords is required for subjective questions.";
                continue;
            }

            $question = Question::query()->create([
                'exam_id' => $this->exam->id,
                'question_text' => $questionText,
                'type' => 'short_answer',
                'marks' => 1,
                'order' => $nextOrder,
                'keywords' => $keywords,
            ]);

            app(QuestionBankService::class)->syncFromQuestion($question);

            $this->imported++;
        }
    }

    /**
     * @param array<int, string> $options
     */
    private function resolveCorrectOptionIndex(string $correctAnswerRaw, array $options): ?int
    {
        if ($correctAnswerRaw === '') {
            return null;
        }

        if (is_numeric($correctAnswerRaw)) {
            $value = (int) $correctAnswerRaw;
            if ($value >= 1 && $value <= 4) {
                return $value - 1;
            }
        }

        foreach ($options as $index => $optionText) {
            if (mb_strtolower($correctAnswerRaw) === mb_strtolower($optionText)) {
                return $index;
            }
        }

        return null;
    }
}
