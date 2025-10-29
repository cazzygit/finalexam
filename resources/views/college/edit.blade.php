@extends('layouts.app')

@section('title', 'Edit College')

@section('content')

<div class="page-header">
    <h1>Edit College</h1>

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
    <div class="card-header">
        <h3>College Information</h3>
        <div class="card-actions">
            <a href="{{ route('colleges.show', $college) }}" class="btn btn-sm btn-outline">
                View Details
            </a>
        </div>
    </div>
    <div class="card-body">
        <form action="{{ route('colleges.update', $college) }}" method="POST" class="form">
            @csrf
            @method('PUT')

            <div class="form-row">
                <div class="form-group">
                    <label for="name" class="form-label required">College Name</label>
                    <input
                        type="text"
                        id="name"
                        name="name"
                        value="{{ old('name', $college->name) }}"
                        placeholder="Enter college name"
                        class="form-input @error('name') is-invalid @enderror"
                        required
                        maxlength="255"
                        autocomplete="organization"
                    >
                    @error('name')
                        <span class="error">{{ $message }}</span>
                    @enderror
                    <small class="form-help">
                        This name must be unique across all colleges in the system.
                    </small>
                </div>
            </div>

            <!-- College Statistics (Read-only information) -->
            <div class="form-section">
                <h4 class="section-title">Current Statistics</h4>
                <div class="stats-grid">
                    <div class="stat-item">
                        <span class="stat-label">Total Sections:</span>
                        <span class="stat-value">{{ $college->sections->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Total Students:</span>
                        <span class="stat-value">{{ $college->students->count() }}</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-label">Created:</span>
                        <span class="stat-value">{{ $college->created_at->format('M j, Y') }}</span>
                    </div>
                    @if($college->updated_at != $college->created_at)
                    <div class="stat-item">
                        <span class="stat-label">Last Updated:</span>
                        <span class="stat-value">{{ $college->updated_at->format('M j, Y g:i A') }}</span>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Warning if college has dependencies -->
            @if($college->sections->count() > 0 || $college->students->count() > 0)
            <div class="warning-box">
                <div class="warning-content">
                    <strong>Important:</strong> This college currently has 
                    {{ $college->sections->count() }} section(s) and 
                    {{ $college->students->count() }} student(s) assigned to it.
                    Changing the college name will affect all related records.
                </div>
            </div>
            @endif

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Update College
                </button>
                <a href="{{ route('colleges.show', $college) }}" class="btn btn-outline">
                    Cancel
                </a>
                <a href="{{ route('colleges.index') }}" class="btn btn-secondary">
                    Back to List
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Related Sections (if any) -->
@if($college->sections->count() > 0)
    <div class="card" style="margin-top: 2rem;">
        <div class="card-header">
            <h3>Sections in this College ({{ $college->sections->count() }})</h3>
        </div>
        <div class="card-body">
            <div class="table-container">
                <table class="table table-sm">
                    <thead>
                        <tr>
                            <th>Section Name</th>
                            <th>Student Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($college->sections->take(5) as $section)
                            <tr>
                                <td><strong>{{ $section->name }}</strong></td>
                                <td>
                                    <span class="badge badge-info">
                                        {{ $section->students->count() }} students
                                    </span>
                                </td>
                                <td>{{ $section->created_at->format('M j, Y') }}</td>
                                <td>
                                    <a href="{{ route('sections.show', $section) }}" class="btn btn-xs btn-outline">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                
                @if($college->sections->count() > 5)
                    <div class="table-footer">
                        <p class="text-muted">
                            Showing 5 of {{ $college->sections->count() }} sections.
                            <a href="{{ route('colleges.show', $college) }}">View all sections</a>
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endif

@endsection
