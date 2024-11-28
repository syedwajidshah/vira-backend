<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;

    protected $primaryKey = 'module_id';

    protected $fillable = [
        'module_title',
        'module_start_date',
        'module_end_date',
        'module_status',
        'sub_project_id',
    ];
    public function subproject()
    {
        return $this->belongsTo(Subproject::class, 'sub_project_id');
    }
    
    public function tickets()
    {
        return $this->hasMany(ProjectTicket::class, 'module_id');
    }
    
  
}
