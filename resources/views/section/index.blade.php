@extends('layouts.app')
<!-- section.index.blade.php -->

@section('title', 'Section Management')

@section('content')

<div class="page-header">
    <h1>Section List</h1>
    <div class="page-actions">
        <a href="{{ route('sections.create') }}" class="btn btn-primary">
            Add New Section
        </a>
    </div>
</div>

<!-- Filter and Pagination Section -->
<div class="filter-pagination-container">
    <div class="filter-section">
        <form action="{{ route('sections.index') }}" method="GET" class="filter-form">
            <label for="college_filter" class="filter-label">Filter by College:</label>
            <select name="college_id" id="college_filter" class="filter-select" onchange="this.form.submit()">
                <option value="">-- All Colleges --</option>
                @foreach($colleges as $college)
                    <option value="{{ $college->id }}" {{ request('college_id') == $college->id ? 'selected' : '' }}>
                        {{ $college->name }}
                    </option>
                @endforeach
            </select>
        </form>
        
        @if(request('college_id'))
            <div class="filter-info">
                <span class="student-count">
                    Showing {{ $sections->total() }} sections
                </span>
                <a href="{{ route('sections.index') }}" class="btn btn-sm btn-outline">
                    Clear Filter
                </a>
            </div>
        @endif
    </div>
    
    @if($sections->count() > 0)
        <div class="pagination-container-top">
            {{ $sections->links() }}
        </div>
    @endif
</div>

@if($sections->count() > 0)
    <div class="table-container">
        <table class="table section-table">
            <thead>
                <tr>
                    <th>Section</th>
                    <th>College</th>
                    <th>Student Count</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sections as $section)
                    <tr>
                        <td>
                            <strong>{{ $section->name }}</strong>
                        </td>
                        <td>
                            <strong>{{ $section->college->name }}</strong>
                        </td>
                        <td>
                            <span class="student-count">
                                {{ $section->students->count() }} students
                            </span>
                        </td>
                        <td>
                            {{ $section->created_at->format('M j, Y') }}
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-outline">
                                    View
                                </a>
                                <a href="{{ route('sections.edit', $section) }}" class="btn btn-sm btn-secondary">
                                    Edit
                                </a>
                                <form action="{{ route('sections.delete', $section) }}" method="POST" style="display:inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
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
        @if(request('college_id'))
            <h3>No sections found for this college</h3>
            <p>Try selecting a different college or create a new section.</p>
            <div style="display: flex; gap: 1rem; justify-content: center; margin-top: 1rem;">
                <a href="{{ route('sections.index') }}" class="btn btn-secondary">
                    View All Sections
                </a>
                <a href="{{ route('sections.create') }}" class="btn btn-primary">
                    Create Section
                </a>
            </div>
        @else
            <h3>No sections found</h3>
            <p>Get started by creating your first section to organize students.</p>
            <a href="{{ route('sections.create') }}" class="btn btn-primary">
                Create First Section
            </a>
        @endif
    </div>
@endif

@endsection