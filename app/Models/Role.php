<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'role',
        'access_level',
    ];

    const ROLE_HR = 'Human Resource';
    const ROLE_LEVEL_ADVISER = 'Level Adviser';
    const ROLE_HOD = 'HOD';
    const ROLE_SUBDEAN = 'Sub-dean';
    const ROLE_DEAN = 'Dean';
    const ROLE_REGISTRAR = 'Registrar';
    const ROLE_SENATE = 'Senate';
    const ROLE_MANAGEMENT = 'Management';
    const ROLE_BOT = 'BOT';
    const ROLE_GOVERNING_COUNCIL = 'Governing Council';
    const ROLE_EXAM_OFFICER = 'Exam Officer';
    const ROLE_VICE_CHANCELLOR = 'Vice Chancellor';
    const ROLE_STUDENT_CARE = 'Student Care';
    const ROLE_PUBLIC_RELATIONS = 'Public Relations';
    const ROLE_BUSARY = 'Bursary';
    const ROLE_ADMISSION = 'Admission';
    const ROLE_GST_CORDINATOR = 'GST Coordinator';
    const ROLE_ICT = 'ICT';
    const ROLE_TAU_VOCATIONS = 'TAU Vocations Coordinator';
    const ROLE_ACADEMIC_PLANNING = 'Academic Planning';


    public static function getRole ($role) {
        if($staffType = self::where('role', $role)->first()) {
            return $staffType->id;
        }
        return null;
    }
}
