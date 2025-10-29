@extends('layouts.app')
<!-- section.edit.blade.php -->

@section('title', 'Edit Class')

@section('content')

<div class="page-header">
    <h1>Edit Class</h1>
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
        <form action="{{ route('sections.update', $section) }}" method="POST">
            @csrf
            @method('PUT')


            <div class="form-group">
                <label for="year" class="form-label">Year</label>
                <select
                    id="year"
                    name="year"
                    class="form-select @error('year') is-invalid @enderror"
                >
                    <option value="">Select Year</option>
                    @for($y=1;$y<=4;$y++)
                        <option value="{{ $y }}" {{ (old('year', $section->year) == $y) ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
                @error('year')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="section" class="form-label">Section</label>
                <input
                    type="text"
                    id="section"
                    name="section"
                    value="{{ old('section', $section->section) }}"
                    placeholder="Enter section (e.g., A, B, C)"
                    class="form-input @error('section') is-invalid @enderror"
                >
                @error('section')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

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
                        <option value="{{ $college->id }}" {{ (old('college_id', $section->college_id) == $college->id) ? 'selected' : '' }}>
                            {{ $college->name }}
                        </option>
                    @endforeach
                </select>
                @error('college_id')
                    <span class="error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Update Class
                </button>
                <a href="{{ route('colleges.show', $section->college_id) }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection