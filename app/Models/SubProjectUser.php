<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubProjectUser extends Model
{
    use HasFactory;

    protected $primaryKey = 'sub_project_user_id';

    protected $fillable = [
        'sub_project_id',
        'user_id',
    ];

    // Optional: Define relationships if you need to access related data
}
