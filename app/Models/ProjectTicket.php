<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProjectTicket extends Model
{
    use HasFactory;

    protected $table = 'project_tickets';
    protected $primaryKey = 'ticket_id';

    protected $fillable = [
        'ticket_title',
        'ticket_start_date',
        'ticket_end_date',
        'ticket_status',
        'module_id',
        'project_ticket_number',
        'ticket_description'
    ];

    // public function getAssignments()
    // {
    //     return AssignTask::where('ticket_id', $this->ticket_id)->get();
    // }
    public function assignments()
{
    return $this->hasMany(AssignTask::class, 'ticket_id', 'ticket_id');
}
public function module()
{
    return $this->belongsTo(Module::class, 'module_id');
}



}
