@extends('layouts.app')
<!-- college.show.blade.php -->

@section('title', 'View College')

@section('content')

<div class="page-header">
    <h1>College: {{ $college->name }}</h1>
    <div class="page-actions">
        <a href="{{ route('colleges.edit', $college) }}" class="btn btn-secondary">
            Edit College
        </a>
        <a href="{{ route('colleges.index') }}" class="btn btn-outline">
            Back to Colleges
        </a>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3>College Overview</h3>
    </div>
    <div class="card-body">
        <ul class="detail-list">
            <li>
                <strong>College Name:</strong>
                <span>{{ $college->name }}</span>
            </li>
            <li>
                <strong>Total Students:</strong>
                <span class="student-count-badge">{{ $college->students->count() }}</span>
            </li>
            <li>
                <strong>Total Classes:</strong>
                <span class="student-count-badge">{{ $college->sections->count() }}</span>
            </li>
            <li>
                <strong>Created Date:</strong>
                <span>{{ $college->created_at->format('F j, Y g:i A') }}</span>
            </li>
            @if($college->updated_at != $college->created_at)
            <li>
                <strong>Last Updated:</strong>
                <span>{{ $college->updated_at->format('F j, Y g:i A') }}</span>
            </li>
            @endif
        </ul>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <div class="card-header" style="display: flex; align-items: center; justify-content: space-between; gap: 1rem; flex-wrap: wrap;">
        <h3>Classes in this College</h3>
        <a href="{{ route('sections.create', ['college_id' => $college->id]) }}" class="btn btn-primary">
            New Class
        </a>
    </div>
    <div class="card-body">
        @if($college->sections->count() > 0)
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>College</th>
                            <th>Number of Students</th>
                            <th>Year & Section</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($college->sections->sortBy('section')->sortBy('year') as $section)
                            <tr>
                                <td><strong>{{ $college->name }}</strong></td>
                                <td>{{ $section->students->count() }} students</td>
                                <td>{{ $section->year ?? '—' }}{{ $section->section ? ' ' . $section->section : '' }}</td>
                                <td>
                                    <div class="table-actions" style="display:flex; gap:.5rem;">
                                        <a href="{{ route('sections.show', $section) }}" class="btn btn-sm btn-outline">View</a>
                                        <a href="{{ route('sections.edit', $section) }}" class="btn btn-sm btn-secondary">Edit</a>
                                        <form action="{{ route('sections.delete', $section) }}" method="POST">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
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
                <h3>No classes found</h3>
                <p>Create a class for this college using the form above.</p>
            </div>
        @endif
    </div>
</div>

@endsection

@push('styles')
<style>
.student-count-badge {
    display: inline-block;
    padding: 0.25rem 0.75rem;
    font-size: 0.875rem;
    font-weight: 500;
    background-color: var(--primary);
    color: white;
    border-radius: 9999px;
}

.text-primary {
    color: var(--primary);
    text-decoration: none;
}

.text-primary:hover {
    text-decoration: underline;
}
</style>
@endpush