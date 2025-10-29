<?php
// CollegeController (add students relationship loading and fix typo)
namespace App\Http\Controllers;

use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;

class CollegeController extends Controller
{
    public function index() {
        try {
            // Order colleges alphabetically by name
            $colleges = College::orderBy('name')->get();
            return view('college.index', compact('colleges'));
        } catch (\Exception $e) {
            Log::error('Error fetching colleges: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load colleges list.');
        }
    }

    public function create() {
        try {
            return view('college.create');
        } catch (\Exception $e) {
            Log::error('Error loading college create form: ' . $e->getMessage());
            return redirect()->route('colleges.index')->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'name' => 'required|unique:colleges,name|string|max:255'
            ]);

            College::create($request->all());

            return redirect()->route('colleges.index')
                ->with('success', 'College created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating college: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create college. Please try again.')
                ->withInput();
        }
    }

    public function edit(College $college) {
        try {
            return view('college.edit', compact('college'));
        } catch (\Exception $e) {
            Log::error('Error loading college edit form: ' . $e->getMessage());
            return redirect()->route('colleges.index')->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, College $college) {
        try {
            $request->validate([
                'name' => [
                    'required',
                    'string',
                    'max:255',
                    Rule::unique('colleges')->ignore($college->id)
                ]
            ]);

            $college->update($request->all());

            return redirect()->route('colleges.index')
                ->with('success', 'College updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating college: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update college. Please try again.')
                ->withInput();
        }
    }

    public function delete(College $college) {
        try {
            $college->delete();

            return redirect()->route('colleges.index')
                ->with('success', 'College deleted successfully!'); // Fixed typo: was "Colleges"

        } catch (\Exception $e) {
            Log::error('Error deleting college: ' . $e->getMessage());
            return redirect()->route('colleges.index')
                ->with('error', 'Failed to delete college. Please try again.');
        }
    }

    public function show(College $college) {
        try {
            // eager load sections and students
            $college->load(['sections', 'students']);
            return view('college.show', compact('college'));
        } catch (\Exception $e) {
            Log::error('Error showing college: ' . $e->getMessage());
            return redirect()->route('colleges.index')->with('error', 'Failed to load college details.');
        }
    }
}