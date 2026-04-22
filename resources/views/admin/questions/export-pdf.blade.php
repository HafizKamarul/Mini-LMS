<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exam Questions Export</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
        }
        h1 {
            font-size: 20px;
            margin-bottom: 4px;
        }
        .meta {
            margin-bottom: 16px;
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 12px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px;
            vertical-align: top;
            text-align: left;
        }
        th {
            background: #f3f4f6;
            font-weight: 700;
        }
        .small {
            color: #6b7280;
            font-size: 11px;
        }
    </style>
</head>
<body>
    <h1>{{ $exam->title }}</h1>
    <div class="meta">Questions and answers export</div>

    <table>
        <thead>
            <tr>
                <th style="width: 28%;">Question</th>
                <th style="width: 10%;">Type</th>
                <th style="width: 30%;">Options</th>
                <th style="width: 12%;">Correct Answer</th>
                <th style="width: 20%;">Keywords</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($questions as $question)
                @php
                    $options = $question->options->sortBy('order')->values();
                    $correctIndex = $options->search(fn ($option) => (bool) $option->is_correct);
                @endphp
                <tr>
                    <td>{{ $question->question_text }}</td>
                    <td>{{ $question->type === 'short_answer' ? 'subjective' : 'mcq' }}</td>
                    <td>
                        @if ($question->type === 'short_answer')
                            <span class="small">N/A</span>
                        @else
                            @foreach ($options as $idx => $option)
                                {{ $idx + 1 }}. {{ $option->option_text }}<br>
                            @endforeach
                        @endif
                    </td>
                    <td>
                        @if ($question->type === 'short_answer')
                            <span class="small">N/A</span>
                        @else
                            {{ $correctIndex === false ? '' : $correctIndex + 1 }}
                        @endif
                    </td>
                    <td>
                        @if ($question->type === 'short_answer')
                            {{ implode(', ', $question->keywords ?? []) }}
                        @else
                            <span class="small">N/A</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">No questions found for this exam.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
