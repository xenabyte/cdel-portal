<?php

namespace App\Models;

use Auth;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StaffProgramAssignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'staff_id',
        'programme_category_id',
        'assigned_by_id',
        'slug',
        'role_in_programme',
        'status',
        'assigned_at',
    ];

    protected $dates = ['assigned_at', 'deleted_at'];

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function programmeCategory()
    {
        return $this->belongsTo(ProgrammeCategory::class, 'programme_category_id');
    }

    public function assignedBy()
    {
        return $this->belongsTo(Admin::class, 'assigned_by_id');
    }
    
}