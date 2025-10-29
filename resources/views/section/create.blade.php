@extends('layouts.app')
<!-- section.create.blade.php -->

@section('title', 'Add Class')

@section('content')

<div class="page-header">
    <h1>Add New Class</h1>
</div>

<div class="card">
    <div class="card-body">
        <form action="{{ route('sections.store') }}" method="POST">
            @csrf


            <div class="form-group">
                <label for="year" class="form-label">Year</label>
                <select
                    id="year"
                    name="year"
                    class="form-select @error('year') is-invalid @enderror"
                    required
                >
                    <option value="">Select Year</option>
                    @for($y=1;$y<=4;$y++)
                        <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
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
                    value="{{ old('section') }}"
                    placeholder="Enter section (e.g., A, B, C)"
                    class="form-input @error('section') is-invalid @enderror"
                    required
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
                        <option value="{{ $college->id }}" {{ (old('college_id', $selectedCollegeId)) == $college->id ? 'selected' : '' }}>
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
                    Save Class
                </button>
                <a href="{{ request('college_id') ? route('colleges.show', request('college_id')) : route('colleges.index') }}" class="btn btn-outline">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

@endsection
