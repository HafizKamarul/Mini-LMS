<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Bulk Upload Questions - {{ $exam->title }}</h1></x-slot>

    <div class="card"><div class="card-body">
        <p class="text-muted">Upload CSV/Excel columns: question, type, option1, option2, option3, option4, correct_answer, keywords.</p>
        <form method="POST" action="{{ route('admin.exams.questions.upload', $exam) }}" enctype="multipart/form-data">
            @csrf
            <div class="mb-3"><label class="form-label" for="questions_file">Questions File</label><input id="questions_file" name="questions_file" type="file" accept=".csv,.xls,.xlsx" class="form-control" required></div>
            <div class="d-flex gap-2"><button class="btn btn-primary" type="submit">Upload & Import</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.questions.index', $exam) }}">Cancel</a></div>
        </form>
    </div></div>
</x-app-layout>
