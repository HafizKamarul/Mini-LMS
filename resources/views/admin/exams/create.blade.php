<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Create Exam</h1></x-slot>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.exams.store') }}">
            @csrf
            <div class="mb-3"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" type="text" value="{{ old('title') }}" required></div>
            <div class="mb-3"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description">{{ old('description') }}</textarea></div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="duration_minutes">Duration (min)</label><input class="form-control" id="duration_minutes" name="duration_minutes" type="number" min="1" value="{{ old('duration_minutes', 60) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="total_marks">Total Marks</label><input class="form-control" id="total_marks" name="total_marks" type="number" min="0" step="0.01" value="{{ old('total_marks', 100) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="starts_at">Starts At</label><input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ old('starts_at') }}"></div>
                <div class="col-md-3"><label class="form-label" for="ends_at">Ends At</label><input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ old('ends_at') }}"></div>
            </div>
            <div class="form-check mt-3"><input class="form-check-input" id="is_published" type="checkbox" name="is_published" value="1" @checked(old('is_published'))><label class="form-check-label" for="is_published">Publish immediately</label></div>
            <div class="mt-4 d-flex gap-2"><button class="btn btn-primary" type="submit">Save Exam</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.index') }}">Cancel</a></div>
        </form>
    </div></div>
</x-app-layout>
