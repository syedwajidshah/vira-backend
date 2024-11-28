<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssignTask extends Model
{
    use HasFactory;

    protected $primaryKey = 'assignment_id';

    protected $fillable = [
        'user_id',
        'ticket_id',
        'assign_by',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'user_id'); 
    }
    public function ticket()
    {
        return $this->belongsTo(ProjectTicket::class, 'ticket_id', 'ticket_id');
    }
}
