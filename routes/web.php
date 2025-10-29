<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CollegeController;
use App\Http\Controllers\SectionController;
use App\Http\Controllers\StudentController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Root route - redirect to colleges
Route::get('/', function () {
    return redirect()->route('colleges.index');
});

/*
|--------------------------------------------------------------------------
| COLLEGE ROUTES
|--------------------------------------------------------------------------
*/

// College index - list all colleges
Route::get('/colleges', [CollegeController::class, 'index'])->name('colleges.index');

// College create routes
Route::get('/colleges/create', [CollegeController::class, 'create'])->name('colleges.create');
Route::post('/colleges', [CollegeController::class, 'store'])->name('colleges.store');

// College show route (must come before edit route to avoid conflicts)
Route::get('/colleges/{college}', [CollegeController::class, 'show'])->name('colleges.show');

// College edit routes
Route::get('/colleges/{college}/edit', [CollegeController::class, 'edit'])->name('colleges.edit');
Route::put('/colleges/{college}', [CollegeController::class, 'update'])->name('colleges.update');

// College delete route
Route::delete('/colleges/{college}', [CollegeController::class, 'delete'])->name('colleges.delete');

/*
|--------------------------------------------------------------------------
| SECTION ROUTES
|--------------------------------------------------------------------------
*/

// Section create routes
Route::get('/sections/create', [SectionController::class, 'create'])->name('sections.create');
Route::post('/sections', [SectionController::class, 'store'])->name('sections.store');

// Section show route
Route::get('/sections/{section}', [SectionController::class, 'show'])->name('sections.show');

// Section edit routes
Route::get('/sections/{section}/edit', [SectionController::class, 'edit'])->name('sections.edit');
Route::put('/sections/{section}', [SectionController::class, 'update'])->name('sections.update');

// Section delete route
Route::delete('/sections/{section}', [SectionController::class, 'delete'])->name('sections.delete');

/*
|--------------------------------------------------------------------------
| STUDENT ROUTES
|--------------------------------------------------------------------------
*/

// Student index - list all students
Route::get('/students', [StudentController::class, 'index'])->name('students.index');

// Student create routes
Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
Route::post('/students', [StudentController::class, 'store'])->name('students.store');

// Student move routes (must come before show route to avoid conflicts)
Route::get('/students/{student}/move', [StudentController::class, 'moveForm'])->name('students.moveForm');
Route::put('/students/{student}/move', [StudentController::class, 'move'])->name('students.move');

// Student show route
Route::get('/students/{student}', [StudentController::class, 'show'])->name('students.show');

// Student edit routes
Route::get('/students/{student}/edit', [StudentController::class, 'edit'])->name('students.edit');
Route::put('/students/{student}', [StudentController::class, 'update'])->name('students.update');

// Student delete route
Route::delete('/students/{student}', [StudentController::class, 'delete'])->name('students.delete');

Route::get('/colleges/{college}/sections', function ($collegeId) {
    return \App\Models\Section::where('college_id', $collegeId)
        ->select('id', 'section', 'year', 'name')
        ->orderBy('section')
        ->orderBy('year')
        ->get();
})->name('colleges.sections');

/*
|--------------------------------------------------------------------------
| OPTIONAL: API-style grouped routes (alternative organization)
|--------------------------------------------------------------------------
| 
| If you prefer a more organized structure, you could group these routes:
|
| Route::prefix('colleges')->name('colleges.')->group(function () {
|     Route::get('/', [CollegeController::class, 'index'])->name('index');
|     Route::get('/create', [CollegeController::class, 'create'])->name('create');
|     Route::post('/', [CollegeController::class, 'store'])->name('store');
|     Route::get('/{college}', [CollegeController::class, 'show'])->name('show');
|     Route::get('/{college}/edit', [CollegeController::class, 'edit'])->name('edit');
|     Route::put('/{college}', [CollegeController::class, 'update'])->name('update');
|     Route::delete('/{college}', [CollegeController::class, 'delete'])->name('delete');
| });
|
*/

/*
|--------------------------------------------------------------------------
| FALLBACK ROUTES
|--------------------------------------------------------------------------
*/

// Handle 404 errors gracefully (optional)
Route::fallback(function () {
    return redirect()->route('colleges.index')->with('error', 'Page not found. Redirected to home page.');
});