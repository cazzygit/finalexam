@extends('layouts.app')
<!-- student.move.blade.php -->

@section('title', 'Move Student')

@section('content')

<div class="page-header">
    <h1>Move Student</h1>
</div>

<div class="card">
    <div class="card-header">
        <h3>Transfer Student to New Section</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <p><strong>Student:</strong> {{ $student->lname }}, {{ $student->fname }} {{ $student->mi }}</p>
            <p><strong>Current College:</strong>
                @if($student->college)
                    <span class="badge">{{ $student->college->name }}</span>
                @else
                    <span class="text-secondary">N/A</span>
                @endif
            </p>
            <p><strong>Current Section:</strong>
                @if($student->section)
                    <span class="badge">{{ $student->section->name }}</span>
                @else
                    <span class="text-secondary">N/A</span>
                @endif
            </p>
        </div>

        <form action="{{ route('students.move', $student) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="college_id" class="form-label">Select New College</label>
                <select
                    name="college_id"
                    id="college_id"
                    class="form-select @error('college_id') is-invalid @enderror"
                    required
                >
                    <option value="">-- Choose College --</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ $student->college_id == $college->id ? 'selected' : '' }}>
                            {{ $college->name }}
                            @if($student->college_id == $college->id)
                                (Current College)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="section_id" class="form-label">Select New Section</label>
                <select
                    name="section_id"
                    id="section_id"
                    class="form-select @error('section_id') is-invalid @enderror"
                    required
                >
                    <option value="">-- Choose Section --</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ $student->section_id == $section->id ? 'disabled' : '' }}>
                            {{ $section->section ?? 'Section ' . $section->id }} ({{ $section->college->name }})
                            @if($student->section_id == $section->id)
                                (Current Section)
                            @endif
                        </option>
                    @endforeach
                </select>
                @error('section_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Move Student
                </button>
                <a href="{{ route('sections.show', $student->section_id) }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('styles')
<style>
.badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    font-weight: 500;
    background-color: var(--primary);
    color: white;
    border-radius: 9999px;
}

.text-secondary {
    color: var(--secondary);
    font-style: italic;
}

option[disabled] {
    opacity: 0.6;
    font-style: italic;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const collegeSelect = document.getElementById('college_id');
    const sectionSelect = document.getElementById('section_id');
    const currentSectionId = '{{ $student->section_id }}';

    function populateSections(collegeId) {
        sectionSelect.innerHTML = '<option value="">-- Choose Section --</option>';
        if (!collegeId) return;

        fetch(`/colleges/${collegeId}/sections`)
            .then(r => r.json())
            .then(data => {
                // data already ordered by section then year (per route)
                data.forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id;
                    const yearText = (s.year != null && s.year !== '') ? `Year ${s.year}` : 'Year —';
                    const secText = (s.section && s.section !== '') ? `Section ${s.section}` : 'Section —';
                    opt.textContent = `${yearText} - ${secText}`;
                    if (String(currentSectionId) === String(s.id)) {
                        opt.disabled = true; // cannot select the current section
                        opt.textContent += ' (Current Section)';
                    }
                    sectionSelect.appendChild(opt);
                });
            })
            .catch(err => console.error('Failed to load sections:', err));
    }

    // Initial population based on currently selected college
    populateSections(collegeSelect.value);

    // When college changes, refresh sections list
    collegeSelect.addEventListener('change', function () {
        populateSections(this.value);
    });
});
</script>
@endpush