<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Add Question - {{ $exam->title }}</h1></x-slot>

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.exams.questions.store', $exam) }}">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="question_type">Question Type</label>
                <select id="question_type" name="question_type" class="form-select" required>
                    <option value="mcq" @selected(old('question_type', 'mcq') === 'mcq')>MCQ</option>
                    <option value="subjective" @selected(old('question_type') === 'subjective')>Subjective</option>
                </select>
            </div>
            <div class="mb-3"><label class="form-label" for="question_text">Question Text</label><textarea id="question_text" name="question_text" class="form-control" rows="4" required>{{ old('question_text') }}</textarea></div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="marks">Marks</label><input id="marks" name="marks" type="number" min="0.25" step="0.25" class="form-control" value="{{ old('marks', 1) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="order">Order</label><input id="order" name="order" type="number" min="0" class="form-control" value="{{ old('order', 0) }}"></div>
            </div>
            <div class="mb-3 mt-3"><label class="form-label" for="explanation">Explanation</label><textarea id="explanation" name="explanation" class="form-control" rows="3">{{ old('explanation') }}</textarea></div>

            <div id="mcq-fields" class="border rounded p-3 mb-3">
                <div class="fw-semibold mb-2">MCQ Options</div>
                @for ($i = 0; $i < 4; $i++)
                    <div class="mb-2"><input class="form-control" name="options[]" type="text" placeholder="Option {{ $i + 1 }}" value="{{ old('options.'.$i) }}"></div>
                @endfor
                <div><label class="form-label" for="correct_option">Correct Option</label><select id="correct_option" name="correct_option" class="form-select"><option value="">Select correct option</option><option value="0" @selected(old('correct_option') === '0')>Option 1</option><option value="1" @selected(old('correct_option') === '1')>Option 2</option><option value="2" @selected(old('correct_option') === '2')>Option 3</option><option value="3" @selected(old('correct_option') === '3')>Option 4</option></select></div>
            </div>

            <div id="subjective-fields" class="border rounded p-3 mb-3">
                <label class="form-label" for="keywords">Keywords (comma separated)</label>
                <input id="keywords" name="keywords" type="text" class="form-control" value="{{ old('keywords') }}">
                <div class="form-text">Example: polymorphism, inheritance, encapsulation</div>
            </div>

            <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Save Question</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.questions.index', $exam) }}">Cancel</a></div>
        </form>
    </div></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const typeSelect = document.getElementById('question_type');
            const mcqFields = document.getElementById('mcq-fields');
            const subjectiveFields = document.getElementById('subjective-fields');
            const toggle = () => {
                const isMcq = typeSelect.value === 'mcq';
                mcqFields.style.display = isMcq ? 'block' : 'none';
                subjectiveFields.style.display = isMcq ? 'none' : 'block';
            };
            typeSelect.addEventListener('change', toggle);
            toggle();
        });
    </script>
</x-app-layout>
