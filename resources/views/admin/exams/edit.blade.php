<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Edit Exam</h1></x-slot>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif

    @php
        $startsAt = old('starts_at', optional($exam->starts_at)->format('Y-m-d\TH:i'));
        $endsAt = old('ends_at', optional($exam->ends_at)->format('Y-m-d\TH:i'));
    @endphp

    <div class="card"><div class="card-body">
        <form method="POST" action="{{ route('admin.exams.update', $exam) }}">
            @csrf @method('PUT')
            <div class="mb-3"><label class="form-label" for="title">Title</label><input class="form-control" id="title" name="title" type="text" value="{{ old('title', $exam->title) }}" required></div>
            <div class="mb-3"><label class="form-label" for="description">Description</label><textarea class="form-control" id="description" name="description">{{ old('description', $exam->description) }}</textarea></div>
            <div class="row g-3">
                <div class="col-md-3"><label class="form-label" for="duration_minutes">Duration (min)</label><input class="form-control" id="duration_minutes" name="duration_minutes" type="number" min="1" value="{{ old('duration_minutes', $exam->duration_minutes) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="total_marks">Total Marks</label><input class="form-control" id="total_marks" name="total_marks" type="number" min="0" step="0.01" value="{{ old('total_marks', $exam->total_marks) }}" required></div>
                <div class="col-md-3"><label class="form-label" for="starts_at">Starts At</label><input class="form-control" id="starts_at" name="starts_at" type="datetime-local" value="{{ $startsAt }}"></div>
                <div class="col-md-3"><label class="form-label" for="ends_at">Ends At</label><input class="form-control" id="ends_at" name="ends_at" type="datetime-local" value="{{ $endsAt }}"></div>
            </div>
            <div class="form-check mt-3"><input class="form-check-input" id="is_published" type="checkbox" name="is_published" value="1" @checked(old('is_published', $exam->is_published))><label class="form-check-label" for="is_published">Published</label></div>
            <div class="mt-4 d-flex gap-2"><button class="btn btn-primary" type="submit">Update Exam</button><a class="btn btn-outline-secondary" href="{{ route('admin.exams.index') }}">Cancel</a></div>
        </form>
    </div></div>
</x-app-layout>
