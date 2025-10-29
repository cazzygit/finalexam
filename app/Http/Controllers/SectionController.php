<?php
// SectionController (updated validation for unique sections per college)
namespace App\Http\Controllers;

use App\Models\Section;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class SectionController extends Controller
{
    public function index(Request $request)
    {
        try {
            $colleges = College::all();

            $perPage = 6;

            $query = Section::with('college');

            if ($request->filled('college_id')) {
                $query->where('college_id', $request->college_id);
            }

            // Order sections alphabetically by section then by year (uses new indexes)
            $sections = $query->orderBy('section')
                             ->orderBy('year')
                             ->paginate($perPage)
                             ->appends($request->all());

            return view('section.index', compact('sections', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error fetching sections: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load sections list.');
        }
    }

    public function create(Request $request) {
        try {
            $colleges = College::all();
            $selectedCollegeId = $request->get('college_id');
            return view('section.create', compact('colleges', 'selectedCollegeId'));
        } catch (\Exception $e) {
            Log::error('Error loading section create form: ' . $e->getMessage());
            return redirect()->route('colleges.index')->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request) {
        try {
            // Log the request data for debugging
            Log::info('Section creation request data:', $request->all());
            
            $request->validate([
                'year' => 'required|integer|min:1|max:4',
                'section' => 'required|string|max:10',
                'college_id' => 'required|exists:colleges,id'
            ]);

            // Generate class name from year and section
            $className = "Year {$request->year} - Section {$request->section}";
            
            // Enforce uniqueness at app layer (matches DB unique)
            $existingClass = Section::where('college_id', $request->college_id)
                ->where('year', $request->year)
                ->where('section', $request->section)
                ->first();
                
            if ($existingClass) {
                return redirect()->back()
                    ->with('error', 'This class already exists in the selected college.')
                    ->withInput();
            }

            // Try to create the section with just the basic fields first
            $section = Section::create([
                'name' => $className,
                'college_id' => $request->college_id
            ]);

            // Update with year and section if they exist in the database
            if (Schema::hasColumn('sections', 'year') && Schema::hasColumn('sections', 'section')) {
                $section->update([
                    'year' => $request->year,
                    'section' => $request->section
                ]);
            }

            return redirect()->route('colleges.show', $section->college_id)
                ->with('success', 'Class created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating section: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            return redirect()->back()
                ->with('error', 'Failed to create section. Please try again.')
                ->withInput();
        }
    }

    public function edit(Section $section) {
        try {
            $colleges = College::all();
            return view('section.edit', compact('section', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error loading section edit form: ' . $e->getMessage());
            return redirect()->route('colleges.show', $section->college_id)->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Section $section) {
        try {
            $request->validate([
                'year' => 'required|integer|min:1|max:4',
                'section' => 'required|string|max:10',
                'college_id' => 'required|exists:colleges,id'
            ]);

            // Enforce uniqueness at app layer (excluding current)
            $existingClass = Section::where('college_id', $request->college_id)
                ->where('year', $request->year)
                ->where('section', $request->section)
                ->where('id', '!=', $section->id)
                ->first();
                
            if ($existingClass) {
                return redirect()->back()
                    ->with('error', 'This class already exists in the selected college.')
                    ->withInput();
            }

            // Generate class name from year and section
            $className = "Year {$request->year} - Section {$request->section}";

            $section->update([
                'name' => $className,
                'year' => $request->year,
                'section' => $request->section,
                'college_id' => $request->college_id
            ]);

            return redirect()->route('colleges.show', $section->college_id)
                ->with('success', 'Class updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating section: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update section. Please try again.')
                ->withInput();
        }
    }

    public function delete(Section $section) {
        try {
            // Check if section has students before deleting
            if ($section->students()->count() > 0) {
                return redirect()->route('colleges.show', $section->college_id)
                    ->with('error', 'Cannot delete section. There are students assigned to this section.');
            }

            $section->delete();

            return redirect()->route('colleges.show', $section->college_id)
                ->with('success', 'Section deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting section: ' . $e->getMessage());
            return redirect()->route('colleges.index')
                ->with('error', 'Failed to delete section. Please try again.');
        }
    }

    public function show(Section $section) {
        try {
            $section->load(['students', 'college']);
            return view('section.show', compact('section'));
        } catch (\Exception $e) {
            Log::error('Error showing section: ' . $e->getMessage());
            return redirect()->route('colleges.index')->with('error', 'Failed to load section details.');
        }
    }
}