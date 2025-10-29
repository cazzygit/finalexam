@extends('layouts.app')
<!-- college.index.blade.php -->

@section('title', 'College Management')

@section('content')

<div class="page-header">
    <h1>College List</h1>
    <div class="page-actions">
        <a href="{{ route('colleges.create') }}" class="btn btn-primary">
            Add New College
        </a>
    </div>
</div>

@if($colleges->count() > 0)
    <div class="table-container">
        <table class="table college-table">
            <thead>
                <tr>
                    <th>College Name</th>
                    <th>Student Count</th>
                    <th>Created Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($colleges as $college)
                    <tr>
                        <td>
                            <strong>{{ $college->name }}</strong>
                        </td>
                        <td>
                            <span class="student-count">
                                {{ $college->students->count() }} students
                            </span>
                        </td>
                        <td>
                            {{ $college->created_at->format('M j, Y') }}
                        </td>
                        <td>
                            <div class="table-actions">
                                <a href="{{ route('colleges.show', $college) }}" class="btn btn-sm btn-outline">
                                    View
                                </a>
                                <a href="{{ route('colleges.edit', $college) }}" class="btn btn-sm btn-secondary">
                                    Edit
                                </a>
                                <form action="{{ route('colleges.delete', $college) }}" method="POST" style="display:inline;">
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
        <h3>No colleges found</h3>
        <p>Get started by creating your first college to organize students.</p>
        <a href="{{ route('colleges.create') }}" class="btn btn-primary">
            Create First College
        </a>
    </div>
@endif

@endsection

@push('styles')
<style>
.student-count {
    color: var(--secondary);
    font-size: 0.875rem;
}
</style>
@endpush