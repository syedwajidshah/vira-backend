<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SubProjectController;
use App\Http\Controllers\ProjectTicketController;
use App\Http\Controllers\ModuleController;

Route::get('/user', function (Request $request) {

});
Route::prefix('projects')->group(function () {
    Route::post('/create-project', [ProjectController::class, 'projectStore'])->name('projects.create');
    Route::get('/all-projects', [ProjectController::class, 'projectList'])->name('projects.list');
    Route::get('/view-project/{id}', [ProjectController::class, 'projectById'])->name('projects.view');
    Route::put('/update-project/{id}', [ProjectController::class, 'projectUpdate'])->name('projects.update');
    Route::delete('/delete-project/{id}', [ProjectController::class, 'projectDelete'])->name('projects.delete');
    Route::get('/{projectId}/progress', [ProjectTicketController::class, 'calculateProgress']);
});

Route::prefix('users')->group(function () {
    Route::post('/create-user', [UserController::class, 'userStore'])->name('users.create');
    Route::get('/all-users', [UserController::class, 'userList'])->name('users.list');
    Route::get('/view-user/{id}', [UserController::class, 'userById'])->name('users.view');
    Route::put('/update-user/{id}', [UserController::class, 'userUpdate'])->name('users.update');
    Route::delete('/delete-user/{id}', [UserController::class, 'userDelete'])->name('users.delete');
    Route::get('/{userId}/progress', [ProjectTicketController::class, 'calculateUserProgress']);
});

Route::prefix('subprojects')->group(function () {
    Route::post('/create-subproject', [SubProjectController::class, 'subprojectStore'])->name('subprojects.create');
    Route::get('/all-subprojects', [SubProjectController::class, 'subprojectList'])->name('subprojects.list');
    Route::get('/view-subproject/{id}', [SubProjectController::class, 'subprojectById'])->name('subprojects.view');
    Route::put('/update-subproject/{id}', [SubProjectController::class, 'subprojectUpdate'])->name('subprojects.update');
    Route::delete('/delete-subproject/{id}', [SubProjectController::class, 'subprojectDelete'])->name('subprojects.delete');
    Route::get('/sub_by_project/{project_id}', [SubProjectController::class, 'getSubprojectsByProjectId'])->name('subprojectsbyid.view');
});




Route::prefix('project-tickets')->group(function () {
    Route::post('/create-ticket', [ProjectTicketController::class, 'ticketStore'])->name('tickets.create');
    Route::get('/all-tickets', [ProjectTicketController::class, 'getAllTicketsWithAssignments'])->name('tickets.list');
    Route::get('/view-ticket/{id}', [ProjectTicketController::class, 'getTicketWithAssignmentsId'])->name('tickets.view');
    Route::put('/update-ticket/{id}', [ProjectTicketController::class, 'updateTicket'])->name('tickets.update');
    Route::delete('/delete-ticket/{id}', [ProjectTicketController::class, 'deleteTicket'])->name('tickets.delete');
    Route::get('/module-tickets/{module_id}', [ProjectTicketController::class, 'getTasksByModuleId'])->name('tickets.by-module');
    Route::put('/ticket-status/{ticket_id}', [ProjectTicketController::class, 'updateTicketStatus']);
    Route::get('/user-tasks/{user_id}', [ProjectTicketController::class, 'getTasksAssignedToUser'])->name('tasks.user');
});


Route::prefix('project-modules')->group(function () {
    Route::post('/create-module', [ModuleController::class, 'moduleStore'])->name('modules.create');
    Route::get('/all-modules', [ModuleController::class, 'moduleList'])->name('modules.list');
    Route::get('/view-module/{id}', [ModuleController::class, 'moduleShow'])->name('modules.view');
    Route::put('/update-module/{id}', [ModuleController::class, 'moduleUpdate'])->name('modules.update');
    Route::delete('/delete-module/{id}', [ModuleController::class, 'moduleDestroy'])->name('modules.delete');
    Route::get('modules_sub_project/{sub_project_id}', [ModuleController::class, 'getModulesBySubproject']);

});
