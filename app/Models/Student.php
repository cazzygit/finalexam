<?php
// Student.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'fname',
        'mi',
        'lname',
        'email',
        'contact',
        'college_id', // Added: student belongs to college
        'section_id',
    ];

    // Student belongs to Section
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    // Student belongs to College (direct relationship)
    public function college()
    {
        return $this->belongsTo(College::class);
    }

    // Accessor for full name
    public function getFullNameAttribute()
    {
        return trim("{$this->fname} {$this->mi} {$this->lname}");
    }
}