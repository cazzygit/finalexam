<?php
// StudentController (add college relationships and validation)
namespace App\Http\Controllers;
use App\Models\Student;
use App\Models\Section;
use App\Models\College;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StudentController extends Controller
{
    public function index(Request $request) {
        try {
            $colleges = College::all();
            $sections = collect();
            if ($request->filled('college_id')) {
                $sections = Section::where('college_id', $request->college_id)
                    ->orderBy('section')
                    ->orderBy('year')
                    ->get();
            } else {
                $sections = Section::with('college')
                    ->orderBy('section')
                    ->orderBy('year')
                    ->get();
            }
            
            $perPage = 10;
            
            $query = Student::with(['section', 'college']);
            
            // Filter by college
            if ($request->filled('college_id')) {
                $query->where('college_id', $request->college_id);
            }
            
            // Filter by section
            if ($request->filled('section_id')) {
                $query->where('section_id', $request->section_id);
            }
            
            // Search by name or student ID
            if ($request->filled('search')) {
                $search = $request->search;
                $query->where(function($q) use ($search) {
                    $q->where('student_id', 'like', '%' . $search . '%')
                      ->orWhere('fname', 'like', '%' . $search . '%')
                      ->orWhere('lname', 'like', '%' . $search . '%')
                      ->orWhere('email', 'like', '%' . $search . '%');
                });
            }
            
            // Make sure to paginate the query, not get() it
            // Order alphabetically by last name then first name
            $students = $query->orderBy('lname')
                             ->orderBy('fname')
                             ->paginate($perPage);
                             
            // Append query parameters to pagination links
            $students->appends($request->query());
            
            return view('student.index', compact('students', 'colleges', 'sections'));
        } catch (\Exception $e) {
            Log::error('Error fetching students: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load students list.');
        }
    }

    public function create() {
        try {
            $sections = Section::with('college')->get();
            $colleges = College::all();
            return view('student.create', compact('sections', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error loading student create form: ' . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'Failed to load create form.');
        }
    }

    public function store(Request $request) {
        try {
            $request->validate([
                'student_id' => 'required|unique:students,student_id|max:10',
                'lname' => 'required|string|max:150',
                'fname' => 'required|string|max:150',
                'mi' => 'nullable|string|max:2', // Fixed: was 'mname'
                'email' => 'required|email|max:150|unique:students,email',
                'contact' => 'required|max:20',
                'college_id' => 'required|exists:colleges,id', // Added college_id validation
                'section_id' => 'required|exists:sections,id'
            ]);

            $student = Student::create($request->all());

            // If student was created from a specific section, redirect back to that section
            if ($request->has('from_section') && $request->from_section) {
                return redirect()->route('sections.show', $student->section_id)
                    ->with('success', 'Student created successfully!');
            }

            return redirect()->route('students.index')
                ->with('success', 'Student created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error creating student: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to create student. Please try again.')
                ->withInput();
        }
    }

    public function edit(Student $student) {
        try {
            $sections = Section::with('college')->get();
            $colleges = College::all();
            return view('student.edit', compact('student', 'sections', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error loading student edit form: ' . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'Failed to load edit form.');
        }
    }

    public function update(Request $request, Student $student) {
        try {
            $request->validate([
                'student_id' => 'required|max:10|unique:students,student_id,' . $student->id,
                'lname' => 'required|string|max:150',
                'fname' => 'required|string|max:150',
                'mi' => 'nullable|string|max:2', // Fixed: was 'mname'
                'email' => 'required|email|max:150|unique:students,email,' . $student->id,
                'contact' => 'required|max:20',
                'college_id' => 'required|exists:colleges,id', // Added college_id validation
                'section_id' => 'required|exists:sections,id'
            ]);

            $student->update($request->all());

            return redirect()->route('students.index')
                ->with('success', 'Student updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            Log::error('Error updating student: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update student. Please try again.')
                ->withInput();
        }
    }

    public function delete(Student $student) {
        try {
            $student->delete();

            return redirect()->route('students.index')
                ->with('success', 'Student deleted successfully!');

        } catch (\Exception $e) {
            Log::error('Error deleting student: ' . $e->getMessage());
            return redirect()->route('students.index')
                ->with('error', 'Failed to delete student. Please try again.');
        }
    }

    public function show(Student $student) {
        try {
            $student->load(['section', 'college']); // eager load both section and college
            return view('student.show', compact('student'));
        } catch (\Exception $e) {
            Log::error('Error showing student: ' . $e->getMessage());
            return redirect()->route('students.index')->with('error', 'Failed to load student details.');
        }
    }

    public function moveForm(Student $student) {
        try {
            $sections = Section::with('college')->orderBy('section')->orderBy('year')->get();
            $colleges = College::orderBy('name')->get();
            return view('student.move', compact('student', 'sections', 'colleges'));
        } catch (\Exception $e) {
            Log::error('Error loading move student form: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to load move student form.');
        }
    }

    public function move(Request $request, Student $student) {
        try {
            $request->validate([
                'college_id' => 'required|exists:colleges,id', // Added college_id validation
                'section_id' => 'required|exists:sections,id'
            ]);

            $student->update([
                'college_id' => $request->college_id, // Update college_id too
                'section_id' => $request->section_id
            ]);

            return redirect()->route('students.index')
                ->with('success', 'Student moved successfully!');
        } catch (\Exception $e) {
            Log::error('Error moving student: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to move student. Please try again.');
        }
    }
}