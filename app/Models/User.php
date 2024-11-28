<?php

// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_name',
        'user_email',
        'user_designation',
        'user_role',
    ];

    protected $dates = ['deleted_at']; // For soft delete handling

    public function assignments()
    {
        return $this->hasMany(AssignTask::class, 'user_id', 'user_id');
    }
}
