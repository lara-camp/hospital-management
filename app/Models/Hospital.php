<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hospital extends Model
{
    use HasFactory;

    public $guarded = ['id'];

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'hospital_doctors', 'hospital_id', 'doctor_id');
    }
    public function patients()
    {
        return $this->hasMany(Patient::class, 'hospital_id');
    }
    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function GetHashTagForHospital($id){
        $hospital = Hospital::find($id);
        $doctors = $hospital->doctors;
        $departmentNames = [];
        foreach ($doctors as $doctor) {
            $department = $doctor->department; // Accessing the department of an individual doctor
            if ($department) {
                $departmentNames[] = $department->name;
            }
        }
        return $departmentNames;
    }
}
