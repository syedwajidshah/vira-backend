<?php

namespace App\Http\Controllers;
use App\Models\Project;
use App\Models\ProjectTicket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use App\Models\AssignTask;
use App\Models\Module; 
use App\Models\Subproject; 
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Exception;

class ProjectTicketController extends Controller
{
    // public function ticketStore(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'ticket_title' => 'required|string|max:255',
    //             'ticket_start_date' => 'required|date',
    //             'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
    //             'ticket_status' => 'required|string',
    //             'module_id' => 'required|integer', // Replaced sub_project_id with module_id
    //             'user_ids' => 'nullable|array',
    //             'user_ids.*' => 'integer|exists:users,user_id',
    //         ]);

    //         $ticket = ProjectTicket::create($validatedData);

    //         if (!empty($validatedData['user_ids'])) {
    //             foreach ($validatedData['user_ids'] as $userId) {
    //                 AssignTask::create([
    //                     'user_id' => $userId,
    //                     'ticket_id' => $ticket->ticket_id,
    //                     'assign_by' => 3,
    //                 ]);
    //             }
    //         }

    //         return response()->json([
    //             'message' => 'Ticket created successfully',
    //             'ticket' => $ticket,
    //             'assignments' => $ticket->getAssignments(),
    //         ], 201);
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation error',
    //             'errors' => $e->errors(),
    //         ], 422);
    //     } catch (Exception $e) {
    //         Log::error('Error creating ticket: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while creating the ticket',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

//     public function ticketStore(Request $request)
// {
//     try {
//         // Validate incoming request data
//         $validatedData = $request->validate([
//             'ticket_title' => 'required|string|max:255',
//             'ticket_start_date' => 'required|date',
//             'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
//             'ticket_status' => 'required|string',
//             'module_id' => 'required|integer', // Replaced sub_project_id with module_id
//             'user_ids' => 'nullable|array',
//             'user_ids.*' => 'integer|exists:users,user_id',
//         ]);

//         // Create the ticket
//         $ticket = ProjectTicket::create($validatedData);

//         // Assign users if user_ids are provided
//         if (!empty($validatedData['user_ids'])) {
//             foreach ($validatedData['user_ids'] as $userId) {
//                 $ticket->assignments()->create([
//                     'user_id' => $userId,
//                     'assign_by' => 3,
//                 ]);
//             }
//         }

//         // Return a response with the created ticket and its assignments
//         return response()->json([
//             'message' => 'Ticket created successfully',
//             'ticket' => $ticket->load('assignments'), // Use the relationship to include assignments
//         ], 201);
//     } catch (ValidationException $e) {
//         return response()->json([
//             'message' => 'Validation error',
//             'errors' => $e->errors(),
//         ], 422);
//     } catch (Exception $e) {
//         Log::error('Error creating ticket: ' . $e->getMessage());
//         return response()->json([
//             'message' => 'An error occurred while creating the ticket',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function ticketStore(Request $request)
{
    try {
        $validatedData = $request->validate([
            'ticket_title' => 'required|string|max:255',
            'ticket_start_date' => 'required|date',
            'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
            'ticket_status' => 'required|string',
            'module_id' => 'required|integer|exists:modules,module_id', 
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,user_id',
            'ticket_description' => 'required|string|max:1000', 
        ]);

        $module = Module::findOrFail($validatedData['module_id']);
        $subproject = Subproject::findOrFail($module->sub_project_id);  
        $project = $subproject->project; 

        $lastProjectTicket = ProjectTicket::whereHas('module.subproject.project', function ($query) use ($project) {
            $query->where('project_id', $project->project_id);
        })
        ->orderBy('project_ticket_number', 'desc')
        ->first();

        $nextTicketNumber = $lastProjectTicket ? $lastProjectTicket->project_ticket_number + 1 : 1;

        $ticket = ProjectTicket::create([
            'ticket_title' => $validatedData['ticket_title'],
            'ticket_start_date' => $validatedData['ticket_start_date'],
            'ticket_end_date' => $validatedData['ticket_end_date'],
            'ticket_status' => $validatedData['ticket_status'],
            'module_id' => $validatedData['module_id'], // Store the module_id
            'project_ticket_number' => $nextTicketNumber,
            'ticket_description' => $validatedData['ticket_description'],
        ]);

        // Update the ticket title with the new task number
        $ticketTitle = "Task#{$nextTicketNumber} {$project->project_name} - {$validatedData['ticket_title']}";
        $ticket->update(['ticket_title' => $ticketTitle]);

        // Assign users if user_ids are provided
        if (!empty($validatedData['user_ids'])) {
            foreach ($validatedData['user_ids'] as $userId) {
                $ticket->assignments()->create([
                    'user_id' => $userId,
                    'assign_by' => 3, // Assuming assign_by is hardcoded or based on a logic
                ]);
            }
        }

        // Return a response with the created ticket and its assignments
        return response()->json([
            'message' => 'Ticket created successfully',
            'ticket' => $ticket->load('assignments'), // Use the relationship to include assignments
        ], 201);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    } catch (Exception $e) {
        Log::error('Error creating ticket: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while creating the ticket',
            'error' => $e->getMessage(),
        ], 500);
    }
}





    public function getAllTicketsWithAssignments()
{
    try {
        $tickets = ProjectTicket::with('assignments')->get();

        return response()->json([
            'message' => 'Tickets retrieved successfully',
            'tickets' => $tickets,
        ], 200);
    } catch (Exception $e) {
        Log::error('Error retrieving tickets with assignments: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while retrieving the tickets',
            'error' => $e->getMessage(),
        ], 500);
    }
}


    // public function getAllTicketsWithAssignments()
    // {
    //     try {
    //         $tickets = ProjectTicket::all();
    //         $ticketsWithAssignments = $tickets->map(function ($ticket) {
    //             $ticket->assignments = $ticket->getAssignments();
    //             return $ticket;
    //         });

    //         return response()->json([
    //             'message' => 'Tickets retrieved successfully',
    //             'tickets' => $ticketsWithAssignments,
    //         ], 200);
    //     } catch (Exception $e) {
    //         Log::error('Error retrieving tickets with assignments: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while retrieving the tickets',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function getTicketWithAssignmentsId($ticket_id)
    // {
    //     try {
    //         $ticket = ProjectTicket::findOrFail($ticket_id);
    //         $ticket->assignments = $ticket->getAssignments();

    //         return response()->json([
    //             'message' => 'Ticket retrieved successfully',
    //             'ticket' => $ticket,
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Ticket not found',
    //             'error' => $e->getMessage(),
    //         ], 404);
    //     } catch (Exception $e) {
    //         Log::error('Error retrieving ticket with assignments: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while retrieving the ticket',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

//     public function getTicketWithAssignmentsId($ticket_id)
// {
//     try {
//         // Use eager loading to retrieve the ticket with its assignments
//         $ticket = ProjectTicket::with('assignments')->findOrFail($ticket_id);

//         return response()->json([
//             'message' => 'Ticket retrieved successfully',
//             'ticket' => $ticket,
//         ], 200);
//     } catch (ModelNotFoundException $e) {
//         return response()->json([
//             'message' => 'Ticket not found',
//             'error' => $e->getMessage(),
//         ], 404);
//     } catch (Exception $e) {
//         return response()->json([
//             'message' => 'An error occurred while retrieving the ticket',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function getTicketWithAssignmentsId($ticket_id)
{
    try {
        // Use eager loading to retrieve the ticket with its assignments and user details
        $ticket = ProjectTicket::with(['assignments.user' => function ($query) {
            $query->select('user_id', 'user_name', 'user_designation'); // Adjust these columns based on your User model
        }])->findOrFail($ticket_id);

        return response()->json([
            'message' => 'Ticket retrieved successfully',
            'ticket' => $ticket,
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Ticket not found',
            'error' => $e->getMessage(),
        ], 404);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while retrieving the ticket',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    // public function updateTicket(Request $request, $ticket_id)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'ticket_title' => 'required|string|max:255',
    //             'ticket_start_date' => 'required|date',
    //             'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
    //             'ticket_status' => 'required|string',
    //             'module_id' => 'required|integer',
    //             'user_ids' => 'nullable|array',
    //             'user_ids.*' => 'integer|exists:users,user_id',
    //         ]);

    //         $ticket = ProjectTicket::findOrFail($ticket_id);
    //         $ticket->update($validatedData);

    //         if (isset($validatedData['user_ids'])) {
    //             AssignTask::where('ticket_id', $ticket_id)->delete();
    //             foreach ($validatedData['user_ids'] as $userId) {
    //                 AssignTask::create([
    //                     'user_id' => $userId,
    //                     'ticket_id' => $ticket->ticket_id,
    //                     'assign_by' => 3,
    //                 ]);
    //             }
    //         }
    //         return response()->json([
    //             'message' => 'Ticket updated successfully',
    //             'ticket' => $ticket,
    //             'assignments' => $ticket->getAssignments(),
    //         ], 200);
    //     } catch (ModelNotFoundException $e) {
    //         return response()->json([
    //             'message' => 'Ticket not found',
    //             'error' => $e->getMessage(),
    //         ], 404);
    //     } catch (ValidationException $e) {
    //         return response()->json([
    //             'message' => 'Validation error',
    //             'errors' => $e->errors(),
    //         ], 422);
    //     } catch (Exception $e) {
    //         Log::error('Error updating ticket and assignments: ' . $e->getMessage());
    //         return response()->json([
    //             'message' => 'An error occurred while updating the ticket',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

//     public function updateTicket(Request $request, $ticket_id)
// {
//     try {
//         // Validate incoming request data
//         $validatedData = $request->validate([
//             'ticket_title' => 'required|string|max:255',
//             'ticket_start_date' => 'required|date',
//             'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
//             'ticket_status' => 'required|string',
//             'module_id' => 'required|integer',
//             'user_ids' => 'nullable|array',
//             'user_ids.*' => 'integer|exists:users,user_id',
//         ]);
//         $ticket = ProjectTicket::findOrFail($ticket_id);

//         $ticket->update($validatedData);

//         if (isset($validatedData['user_ids'])) {
//             $ticket->assignments()->delete();

//             foreach ($validatedData['user_ids'] as $userId) {
//                 $ticket->assignments()->create([
//                     'user_id' => $userId,
//                     'assign_by' => 3,
//                 ]);
//             }
//         }

//         return response()->json([
//             'message' => 'Ticket updated successfully',
//             'ticket' => $ticket->load('assignments'), 
//         ], 200);
//     } catch (ModelNotFoundException $e) {
//         return response()->json([
//             'message' => 'Ticket not found',
//             'error' => $e->getMessage(),
//         ], 404);
//     } catch (ValidationException $e) {
//         return response()->json([
//             'message' => 'Validation error',
//             'errors' => $e->errors(),
//         ], 422);
//     } catch (Exception $e) {
//         Log::error('Error updating ticket and assignments: ' . $e->getMessage());
//         return response()->json([
//             'message' => 'An error occurred while updating the ticket',
//             'error' => $e->getMessage(),
//         ], 500);
//     }
// }

public function updateTicket(Request $request, $ticket_id)
{
    try {
        // Validate incoming request data
        $validatedData = $request->validate([
            'ticket_title' => 'required|string|max:255',
            'ticket_start_date' => 'required|date',
            'ticket_end_date' => 'required|date|after_or_equal:ticket_start_date',
            'ticket_status' => 'required|string',
            'module_id' => 'required|integer|exists:modules,module_id',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|exists:users,user_id',
            'ticket_description' => 'nullable|string|max:1000',
        ]);

        // Find the ticket or throw an exception if not found
        $ticket = ProjectTicket::findOrFail($ticket_id);

        // Retrieve related project and module information
        // $module = Module::findOrFail($ticket->module_id);
        // $subproject = Subproject::findOrFail($module->sub_project_id);
        // $project = $subproject->project;

        // // Extract the task number and project name from the existing title
        // $taskNumber = $ticket->project_ticket_number;
        // $projectName = $project->project_name;

        // // Create the updated title while preserving the task number and project name
        // $updatedTitle = "Task#{$taskNumber} {$projectName} - {$validatedData['ticket_title']}";

        // Update the ticket fields
        $ticket->update([
            // 'ticket_title' => $updatedTitle,
            'ticket_title' => $validatedData['ticket_title'],
            'ticket_start_date' => $validatedData['ticket_start_date'],
            'ticket_end_date' => $validatedData['ticket_end_date'],
            'ticket_status' => $validatedData['ticket_status'],
            'module_id' => $validatedData['module_id'],
            'ticket_description' => $validatedData['ticket_description'] ?? $ticket->ticket_description,
        ]);

        // Update the assignments if user_ids are provided
        if (isset($validatedData['user_ids'])) {
            $ticket->assignments()->delete(); // Remove existing assignments

            foreach ($validatedData['user_ids'] as $userId) {
                $ticket->assignments()->create([
                    'user_id' => $userId,
                    'assign_by' => 3,
                ]);
            }
        }

        // Return success response
        return response()->json([
            'message' => 'Ticket updated successfully',
            'ticket' => $ticket->load('assignments'), // Include assignments in the response
        ], 200);
    } catch (ModelNotFoundException $e) {
        return response()->json([
            'message' => 'Ticket not found',
            'error' => $e->getMessage(),
        ], 404);
    } catch (ValidationException $e) {
        return response()->json([
            'message' => 'Validation error',
            'errors' => $e->errors(),
        ], 422);
    } catch (Exception $e) {
        Log::error('Error updating ticket: ' . $e->getMessage());
        return response()->json([
            'message' => 'An error occurred while updating the ticket',
            'error' => $e->getMessage(),
        ], 500);
    }
}



    public function deleteTicket($ticket_id)
    {
        try {
            $ticket = ProjectTicket::findOrFail($ticket_id);
            AssignTask::where('ticket_id', $ticket_id)->delete();
            $ticket->delete();

            return response()->json([
                'message' => 'Ticket and its assignments deleted successfully',
            ], 200);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'message' => 'Ticket not found',
                'error' => $e->getMessage(),
            ], 404);
        } catch (Exception $e) {
            Log::error('Error deleting ticket and its assignments: ' . $e->getMessage());
            return response()->json([
                'message' => 'An error occurred while deleting the ticket',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    // public function getTasksByModuleId($module_id)
    // {
    //     try {
    //         // Fetch tickets for the specified module along with their assignments
    //         $tickets = ProjectTicket::where('module_id', $module_id)
    //             ->with('assignments') // Eager load the assignments relationship
    //             ->get();
    
    //         if ($tickets->isEmpty()) {
    //             return response()->json([
    //                 'message' => 'No tickets found for the specified module',
    //             ], 404);
    //         }
    
    //         return response()->json([
    //             'message' => 'Tickets retrieved successfully',
    //             'tickets' => $tickets,
    //         ], 200);
    //     } catch (Exception $e) {
    //         return response()->json([
    //             'message' => 'An error occurred while retrieving tickets',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
    
    public function getTasksByModuleId($module_id)
    {
        try {
            $tickets = ProjectTicket::where('module_id', $module_id)
                ->with(['assignments.user']) 
                ->get();
    
            if ($tickets->isEmpty()) {
                return response()->json([
                    'message' => 'No tickets found for the specified module',
                ], 404);
            }
    
            return response()->json([
                'message' => 'Tickets retrieved successfully',
                'tickets' => $tickets,
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving tickets',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    

    public function updateTicketStatus(Request $request, $ticket_id)
{
    try {
        // Validate the request input
        $validated = $request->validate([
            'ticket_status' => 'required|string|in:pending,progress,complete,quality', // Define the allowed status values
        ]);
        
        // Find the ticket by ID
        $ticket = ProjectTicket::find($ticket_id);

        // Check if the ticket exists
        if (!$ticket) {
            return response()->json([
                'message' => 'Ticket not found',
            ], 404);
        }

        // Update the status of the ticket
        $ticket->ticket_status = $validated['ticket_status'];
        $ticket->save();

        return response()->json([
            'message' => 'Ticket status updated successfully',
            'ticket' => $ticket,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while updating the ticket status',
            'error' => $e->getMessage(),
        ], 500);
    }
}



public function getTasksAssignedToUser($user_id)
{
    try {
        $assignments = AssignTask::where('user_id', $user_id)
            ->with('user', 'ticket')  
            ->get();
        if ($assignments->isEmpty()) {
            return response()->json([
                'message' => 'No tasks assigned to this user.',
                'data' => [],
            ], 404);
        }
        return response()->json([
            'message' => 'Tasks fetched successfully',
            'data' => $assignments,
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'An error occurred while fetching tasks.',
        ], 500);
    }
}

public function calculateProgress($projectId)
{
    // Find the project
    $project = Project::with('subprojects.modules.tickets')->find($projectId);

    if (!$project) {
        return response()->json(['message' => 'Project not found'], 404);
    }

    // Initialize counters
    $totalTickets = 0;
    $completedTickets = 0;

    // Loop through the subprojects and modules to get tickets
    foreach ($project->subprojects as $subproject) {
        foreach ($subproject->modules as $module) {
            foreach ($module->tickets as $ticket) {
                // Count total tickets
                $totalTickets++;

                // Count completed tickets
                if ($ticket->ticket_status === 'complete') {
                    $completedTickets++;
                }
            }
        }
    }

    // Calculate the progress percentage
    $progress = $totalTickets > 0 ? ($completedTickets / $totalTickets) * 100 : 0;

    // Return the progress as a JSON response
    return response()->json([
        'project_id' => $project->project_id,
        'project_name' => $project->project_name,
        'total_tickets' => $totalTickets,
        'completed_tickets' => $completedTickets,
        'progress_percentage' => number_format($progress, 2),
    ]);
}

public function calculateUserProgress($userId)
{
    // Find the user
    $user = User::with('assignments.ticket')->find($userId);

    if (!$user) {
        return response()->json(['message' => 'User not found'], 404);
    }

    // Initialize counters
    $totalAssignments = 0;
    $completedAssignments = 0;

    // Loop through the assignments and check ticket completion
    foreach ($user->assignments as $assignment) {
        $totalAssignments++;

        // Count completed assignments based on ticket status
        if ($assignment->ticket->ticket_status === 'complete') {
            $completedAssignments++;
        }
    }

    // Calculate the progress percentage
    $progress = $totalAssignments > 0 ? ($completedAssignments / $totalAssignments) * 100 : 0;

    // Return the progress as a JSON response
    return response()->json([
        'user_id' => $user->user_id,
        'user_name' => $user->user_name,
        'total_assignments' => $totalAssignments,
        'completed_assignments' => $completedAssignments,
        'progress_percentage' => number_format($progress, 2),
        
    ]);
}
}
