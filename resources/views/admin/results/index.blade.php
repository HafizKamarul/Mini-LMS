<x-app-layout>
    <x-slot name="header"><h1 class="h4 mb-0">Results</h1></x-slot>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-striped mb-0">
                <thead><tr><th>Exam</th><th>Student</th><th>Score</th><th>Percentage</th><th>Time Taken</th><th>Published</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                @forelse($results as $result)
                    @php $minutes = intdiv((int) $result->time_taken, 60); $seconds = (int) $result->time_taken % 60; @endphp
                    <tr>
                        <td>{{ $result->exam->title }}</td>
                        <td>{{ $result->student->name }}<div class="small text-muted">{{ $result->student->email }}</div></td>
                        <td>{{ number_format((float) $result->score, 2) }} / {{ number_format((float) $result->total_marks, 2) }}</td>
                        <td>{{ number_format((float) $result->percentage, 2) }}%</td>
                        <td>{{ sprintf('%02d:%02d', $minutes, $seconds) }}</td>
                        <td>{{ $result->published_at ? 'Yes' : 'No' }}</td>
                        <td class="text-end">
                            <form action="{{ route('admin.results.override', $result) }}" method="POST" class="d-inline-flex gap-2 mb-2">
                                @csrf @method('PATCH')
                                <input type="number" name="score" min="0" max="{{ $result->total_marks }}" step="0.01" value="{{ $result->score }}" class="form-control form-control-sm" style="width:110px" required>
                                <button class="btn btn-sm btn-outline-primary" type="submit">Override</button>
                            </form>
                            <form action="{{ route('admin.results.publish.toggle', $result) }}" method="POST">
                                @csrf @method('PATCH')
                                <button class="btn btn-sm {{ $result->published_at ? 'btn-outline-warning' : 'btn-outline-success' }}" type="submit">{{ $result->published_at ? 'Unpublish' : 'Publish' }}</button>
                            </form>
                            <a href="{{ route('admin.results.show', $result) }}" class="btn btn-sm btn-outline-secondary mt-2">View</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center text-muted">No results found.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $results->links() }}</div>
</x-app-layout>
