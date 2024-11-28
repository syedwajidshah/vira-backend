<?php

// app/Models/SubProject.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SubProject extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'sub_project_id';

    protected $fillable = [
        'sub_project_name',
        'sub_project_start_date',
        'sub_project_end_date',
        'sub_project_status',
        'sub_project_manager',
        'project_id',
    ];

    public function assignedUsers()
{
    return $this->belongsToMany(User::class, 'sub_project_users', 'sub_project_id', 'user_id');
}
public function modules()
{
    return $this->hasMany(Module::class, 'sub_project_id');
}

public function project()
{
    return $this->belongsTo(Project::class, 'project_id');
}


}
