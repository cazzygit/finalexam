@extends('layouts.app')
<!-- student.create.blade.php -->

@section('title', 'Add Student')

@section('content')

<div class="page-header">
    <h1>Add New Student</h1>
</div>

@if ($errors->any())
    <div class="error-box">
        <h3>Please fix the following errors:</h3>
        <ul class="error-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="card">
    <div class="card-body">
        <form action="{{ route('students.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="student_id" class="form-label">Student Number</label>
                <input
                    type="text"
                    id="student_id"
                    name="student_id"
                    value="{{ old('student_id') }}"
                    placeholder="Enter student number"
                    class="form-input @error('student_id') is-invalid @enderror"
                    required
                >
                @error('student_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="fname" class="form-label">First Name</label>
                <input
                    type="text"
                    id="fname"
                    name="fname"
                    value="{{ old('fname') }}"
                    placeholder="Enter first name"
                    class="form-input @error('fname') is-invalid @enderror"
                    required
                >
                @error('fname')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="mi" class="form-label">Middle Initial</label>
                <input
                    type="text"
                    id="mi"
                    name="mi"
                    value="{{ old('mi') }}"
                    placeholder="Enter middle initial (optional)"
                    class="form-input @error('mi') is-invalid @enderror"
                >
                @error('mi')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="lname" class="form-label">Last Name</label>
                <input
                    type="text"
                    id="lname"
                    name="lname"
                    value="{{ old('lname') }}"
                    placeholder="Enter last name"
                    class="form-input @error('lname') is-invalid @enderror"
                    required
                >
                @error('lname')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Enter email address"
                    class="form-input @error('email') is-invalid @enderror"
                    required
                >
                @error('email')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="contact" class="form-label">Contact Number</label>
                <input
                    type="text"
                    id="contact"
                    name="contact"
                    value="{{ old('contact') }}"
                    placeholder="Enter contact number"
                    class="form-input @error('contact') is-invalid @enderror"
                    required
                >
                @error('contact')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            @if(request('section_id'))
                @php($selectedSection = \App\Models\Section::with('college')->find(request('section_id')))
                @if($selectedSection)
                    <div class="form-group">
                        <label class="form-label">Class</label>
                        <div class="form-display">
                            <strong>{{ $selectedSection->name ?? 'Year ' . $selectedSection->year . ' - Section ' . $selectedSection->section }}</strong>
                            <small class="text-muted">(Automatically assigned)</small>
                        </div>
                    </div>
                @endif
            @endif

            <div class="form-group">
                <label for="college_id" class="form-label">College</label>
                <select
                    id="college_id"
                    name="college_id"
                    class="form-select @error('college_id') is-invalid @enderror"
                    required
                >
                    <option value="">Select College</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ (old('college_id', request('college_id')) == $college->id) ? 'selected' : '' }}>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            @if(!request('section_id'))
                <div class="form-group">
                    <label for="section_id" class="form-label">Class</label>
                    <select
                        id="section_id"
                        name="section_id"
                        class="form-select @error('section_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Select Class</option>
                    </select>
                    @error('section_id')
                        <span class="error">{{ $message }}</span>
                    @enderror
                </div>
            @else
                <input type="hidden" name="section_id" value="{{ request('section_id') }}">
                @if(request('from_section'))
                    <input type="hidden" name="from_section" value="{{ request('from_section') }}">
                @endif
            @endif


            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Save Student
                </button>
                <a href="{{ route('students.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const collegeSelect = document.getElementById("college_id");
    const sectionSelect = document.getElementById("section_id");
    let sectionsData = []; // Store sections data globally

    function populateSections(collegeId, selectedSectionId) {
        sectionSelect.innerHTML = '<option value="">Select Section</option>';
        if (collegeId) {
            fetch(`/colleges/${collegeId}/sections`)
                .then(response => response.json())
                .then(data => {
                    sectionsData = data; // Store the data
                    console.log('Sections data loaded:', data);
                    
                    // Sort sections by year then by section
                    data.sort((a, b) => {
                        if (a.year !== b.year) return a.year - b.year;
                        return (a.section || '').localeCompare(b.section || '');
                    });
                    
                    data.forEach(section => {
                        let option = document.createElement("option");
                        option.value = section.id;
                        option.textContent = `Year ${section.year} - Section ${section.section}`;
                        if (String(selectedSectionId || '') === String(section.id)) option.selected = true;
                        sectionSelect.appendChild(option);
                    });
                })
                .catch(error => console.error('Error fetching sections:', error));
        }
    }

    collegeSelect.addEventListener("change", function () {
        let collegeId = this.value;
        // Only populate sections if section is not readonly (not coming from specific section)
        if (!sectionSelect.hasAttribute('readonly')) {
            populateSections(collegeId);
        }
    });


    // Prefill when landed from a section page
    const preCollegeId = "{{ request('college_id') }}";
    const preSectionId = "{{ request('section_id') }}";
    if (preCollegeId && preSectionId) {
        // If coming from a specific section, populate with that section
        populateSections(preCollegeId, preSectionId);
    } else if (preCollegeId) {
        // If only college is pre-selected, populate all sections for that college
        populateSections(preCollegeId);
    }

});
</script>
@endpush

@push('styles')
<style>
select[readonly] {
    background-color: #f8f9fa;
    cursor: not-allowed;
    opacity: 0.7;
}

.form-display {
    padding: 0.75rem 1rem;
    background-color: #f8f9fa;
    border: 1px solid #e9ecef;
    border-radius: 0.375rem;
    color: #495057;
}

.form-display strong {
    color: #212529;
    font-weight: 600;
}

.form-display .text-muted {
    color: #6c757d;
    font-size: 0.875rem;
    margin-left: 0.5rem;
}
</style>
@endpush
