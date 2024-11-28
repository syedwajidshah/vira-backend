<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'project_id';

    protected $fillable = [
        'project_name',
        'project_start_date',
        'project_end_date',
        'project_status',
        'project_manager',
    ];

    public function subprojects()
{
    return $this->hasMany(Subproject::class, 'project_id');
}

public function tickets()
{
    return $this->hasManyThrough(ProjectTicket::class, Module::class, 'sub_project_id', 'module_id');
}

}
