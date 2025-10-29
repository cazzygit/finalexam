@extends('layouts.app')
<!-- college.create.blade.php -->
@section('title', 'Add College')

@section('content')

<div class="page-header">
    <h1>Add New College</h1>
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
        <form action="{{ route('colleges.store') }}" method="POST">
            @csrf

            <div class="form-group">
                <label for="name" class="form-label">College Name</label>
                <input
                    type="text"
                    id="name"
                    name="name"
                    value="{{ old('name') }}"
                    placeholder="Enter college name"
                    class="form-input @error('name') is-invalid @enderror"
                    required
                >
                @error('name')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Save College
                </button>
                <a href="{{ route('colleges.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
