@extends('layouts.app')
<!-- student.index.blade.php -->

@section('title', 'Student Management')

@section('content')

<div class="page-header">
    <h1>Student List</h1>
    <div class="page-actions">
        <a href="{{ route('students.create') }}" class="btn btn-primary">
            Add New Student
        </a>
    </div>
</div>

<!-- Filter and Pagination Section -->
<div class="filter-pagination-container">
    <div class="filter-section">
        <form action="{{ route('students.index') }}" method="GET" class="filter-form" id="studentFilters">
            <!-- Search Input -->
            <div class="search-group">
                <label for="search" class="filter-label">Search:</label>
                <input type="text" 
                       name="search" 
                       id="search" 
                       class="form-input search-input" 
                       placeholder="Name, ID, or email..." 
                       value="{{ request('search') }}">
            </div>

            <!-- College Filter -->
            <div class="filter-group">
                <label for="college_filter" class="filter-label">College:</label>
                <select name="college_id" id="college_filter" class="filter-select">
                    <option value="">-- All Colleges --</option>
                    @foreach($colleges as $college)
                        <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Section Filter -->
            <div class="filter-group">
                <label for="section_filter" class="filter-label">Section:</label>
                <select name="section_id" id="section_filter" class="filter-select">
                    <option value="">-- All Sections --</option>
                    @foreach($sections as $section)
                        <option value="{{ $section->id }}" {{ request('section_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->name }}@if(!request('college_id')) ({{ $section->college->name }})@endif
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
        
        @if(request()->hasAny(['college_id', 'section_id', 'search']))
            <div class="filter-info">
                <span class="student-count">
                    Showing {{ $students->total() }} students
                </span>
                <a href="{{ route('students.index') }}" class="btn btn-sm btn-outline">Reset Filters</a>
            </div>
        @endif
    </div>
    
    @if($students->count() > 0)
        <div class="pagination-container-top">
            {{ $students->links() }}
        </div>
    @endif
</div>

@if($students->count() > 0)
    <div class="table-container">
        <table class="table student-table">
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Year</th>
                    <th>College</th>
                    <th>Section</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $student)
                    <tr>
                        <td><strong>{{ $student->student_id }}</strong></td>
                        <td>{{ $student->full_name }}</td>
                        <td>{{ $student->section->year ?? '—' }}</td>
                        <td>
                            @if($student->college)
                                <span>{{ $student->college->name }}</span>
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($student->section)
                                <span>{{ $student->section->section ?? 'Section ' . $student->section->id }}</span>
                            @else
                                <span class="text-secondary">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('students.show', $student) }}" class="btn btn-sm btn-outline">
                                    View
                                </a>
                                <a href="{{ route('students.edit', $student) }}" class="btn btn-sm btn-secondary">
                                    Edit
                                </a>
                                <form action="{{ route('students.delete', $student) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this student?')">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <div class="empty-state">
        @if(request()->hasAny(['college_id', 'section_id', 'search']))
            <h3>No students found</h3>
            <p>Try adjusting your filters or create a new student.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem;">
                <a href="{{ route('students.index') }}" class="btn btn-secondary">
                    View All Students
                </a>
                <a href="{{ route('students.create') }}" class="btn btn-primary">
                    Add New Student
                </a>
            </div>
        @else
            <h3>No students found</h3>
            <p>Get started by adding your first student to the system.</p>
            <a href="{{ route('students.create') }}" class="btn btn-primary">
                Add New Student
            </a>
        @endif
    </div>
@endif

@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('studentFilters');
    const college = document.getElementById('college_filter');
    const section = document.getElementById('section_filter');
    const search = document.getElementById('search');

    function submitForm() {
        form.submit();
    }

    let debounce;
    function debounceSubmit() {
        clearTimeout(debounce);
        debounce = setTimeout(submitForm, 400);
    }

    college.addEventListener('change', function() {
        // When college changes, clear section and submit
        section.value = '';
        submitForm();
    });

    section.addEventListener('change', submitForm);
    search.addEventListener('input', debounceSubmit);
});
</script>
@endpush

