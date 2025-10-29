<?php
// Section.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'year', 'section', 'college_id'];

    // Section belongs to College
    public function college()
    {
        return $this->belongsTo(College::class);
    }
    
    // One Section has many Students
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    /**
     * Validation rules helper aligned with DB constraints: (college_id, year, section) unique
     */
    public static function getValidationRules(?int $sectionId = null): array
    {
        return [
            'college_id' => 'required|exists:colleges,id',
            'year' => 'required|integer|min:1|max:4',
            'section' => [
                'required',
                'string',
                'max:10',
                \Illuminate\Validation\Rule::unique('sections')->where(function ($q) {
                    return $q->where('college_id', request('college_id'))
                             ->where('year', request('year'))
                             ->where('section', request('section'));
                })->ignore($sectionId)
            ],
        ];
    }

    /**
     * Scope: default ordering by section then year for stable A–Z + level
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('section')->orderBy('year');
    }
}