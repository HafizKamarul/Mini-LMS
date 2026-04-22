<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Bulk Upload Questions - {{ $exam->title }}</h1></x-slot>

    <div class="card border-0 shadow-sm mb-3">
        <div class="card-body">
            <h2 class="h6 mb-3">Required File Format</h2>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead>
                        <tr>
                            <th>Column</th>
                            <th>Required</th>
                            <th>Accepted Values / Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr><td>question</td><td>Yes</td><td>Question text.</td></tr>
                        <tr><td>type</td><td>Yes</td><td>Use mcq or subjective.</td></tr>
                        <tr><td>option1</td><td>MCQ only</td><td>Choice A text.</td></tr>
                        <tr><td>option2</td><td>MCQ only</td><td>Choice B text.</td></tr>
                        <tr><td>option3</td><td>MCQ only</td><td>Choice C text.</td></tr>
                        <tr><td>option4</td><td>MCQ only</td><td>Choice D text.</td></tr>
                        <tr><td>correct_answer</td><td>MCQ only</td><td>Use 1, 2, 3, 4 or exact option text.</td></tr>
                        <tr><td>keywords</td><td>Subjective only</td><td>Comma-separated keywords, e.g. inheritance, polymorphism.</td></tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-info mt-3 mb-0">
                <strong>Example rows:</strong><br>
                <span class="small">What is 2 + 2?,mcq,3,4,5,6,2,</span><br>
                <span class="small">Explain OOP pillars,subjective,,,,,,encapsulation, inheritance, polymorphism, abstraction</span>
            </div>
        </div>
    </div>

    <div class="card border-0 shadow-sm"><div class="card-body">
        <form method="POST" action="{{ route('admin.exams.questions.upload', $exam) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label" for="questions_file">Questions File (CSV, XLS, XLSX)</label><input id="questions_file" name="questions_file" type="file" accept=".csv,.xls,.xlsx" class="form-control" required></div>
            <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Upload and Import</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.questions.index', $exam) }}">Cancel</a></div>
        </form>
    </div></div>
</x-app-layout>
