<?php
// College.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class College extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    // One College has many Sections
    public function sections()
    {
        return $this->hasMany(Section::class);
    }

    // One College has many Students (direct relationship)
    public function students()
    {
        return $this->hasMany(Student::class);
    }
}