<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Edit Question</h1></x-slot>

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.exams.questions.update', [$exam, $question]) }}">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label" for="question_text">Question Text</label><textarea id="question_text" name="question_text" class="form-control" rows="4" required>{{ old('question_text', $question->question_text) }}</textarea></div>
            <div class="row g-3 mb-3">
                <div class="col-md-3"><label class="form-label" for="marks">Marks</label><input id="marks" name="marks" type="number" min="0.25" step="0.25" class="form-control" value="{{ old('marks', $question->marks) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="order">Order</label><input id="order" name="order" type="number" min="0" class="form-control" value="{{ old('order', $question->order) }}"></div>
            </div>

            @if ($question->type === 'single_choice')
                @php $options = $question->options->sortBy('order')->values(); $correctIndex = $options->search(fn($o) => $o->is_correct); @endphp
                <div class="border rounded p-3 mb-3">
                    <div class="fw-semibold mb-2">Options</div>
                    @foreach ($options as $i => $option)
                        <div class="input-group mb-2">
                            <span class="input-group-text"><input type="radio" name="correct_option" value="{{ $i }}" {{ old('correct_option', $correctIndex) == $i ? 'checked' : '' }}></span>
                            <input type="text" name="options[{{ $i }}]" value="{{ old("options.$i", $option->option_text) }}" class="form-control" required>
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($question->type === 'short_answer')
                <div class="mb-3"><label class="form-label" for="keywords">Keywords</label><input id="keywords" name="keywords" type="text" class="form-control" value="{{ old('keywords', implode(', ', $question->keywords ?? [])) }}" required></div>
            @endif

            <div class="mb-3"><label class="form-label" for="explanation">Explanation</label><textarea id="explanation" name="explanation" class="form-control" rows="3">{{ old('explanation', $question->explanation) }}</textarea></div>
            <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Save Changes</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.questions.index', $exam) }}">Cancel</a></div>
        </form>
    </div></div>
</x-app-layout>
