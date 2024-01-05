<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Models\Task;
use App\Models\Note;
use App\Http\Controllers\API\BaseController as BaseController;
use App\Http\Resources\TaskResource;
use Validator;
use Illuminate\Http\JsonResponse;

class TaskController extends BaseController
{
    public function createTask(Request $request)
    {
      
       $input = $request->all();
        $validator = Validator::make($input, [
            'subject' => 'required|string',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'due_date' => 'required|date',
            'status' => 'required|in:New,Incomplete,Complete',
            'priority' => 'required|in:High,Medium,Low',
            'notes' => 'array',
            'notes.*.subject' => 'string',
            'notes.*.note' => 'string',
            'notes.*.attachments.*' => 'file|mimes:jpeg,png,pdf,docx|max:2048',

        ]);
       
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $task = Task::create([
            'subject' => $request->input('subject'),
            'description' => $request->input('description'),
            'start_date' => $request->input('start_date'),
            'due_date' => $request->input('due_date'),
            'status' => $request->input('status'),
            'priority' => $request->input('priority'),
        ]);
        if($request->has('notes')){
            foreach ($request->input('notes') as $index => $noteData) {
                $note = new Note([
                    'subject' => $noteData['subject'],
                    'note' => $noteData['note'],
                ]);
                $pathArr = [];
                $attachments = $request->file("notes.$index.attachments");
               
                if (isset($attachments)) {
                    foreach ($attachments as $attachment) {
                        $path = $attachment->store('attachments','public');
                        $pathArr[] = $path;
                    }
                }
                $note->attachments = $pathArr;
                $task->notes()->save($note);
            }
    
        }

        return $this->sendResponse([], 'Task created successfully.');
 
    }
    public function getAllTasksWithNotes(Request $request)
    {
        $query = Task::with('notes');
        
        if ($request->has('filter')) {
         
            $filters = $request->input('filter');

            if (isset($filters['status'])) {
                $query->where('status', $filters['status']);
            }

            if (isset($filters['due_date'])) {
                $query->where('due_date', $filters['due_date']);
            }

            if (isset($filters['priority'])) {
                $query->where('priority', $filters['priority']);
            }

            if (isset($filters['notes']) && $filters['notes'] == 1) {
                $query->has('notes');
            }
        }

        $tasksWithNotes = $query->withCount('notes')
                            ->orderByRaw("FIELD(priority, 'High', 'Medium', 'Low')")
                            ->get();
        if ($tasksWithNotes->isEmpty()) {
            return $this->sendResponse([], 'NO Tasks found!!');
        }
        
        return $this->sendResponse(TaskResource::collection($tasksWithNotes), 'Task retrived successfully.');
    }

    public function getTaskByNoteCount(request $request){
       $tasksWithNotes = Task::withCount('notes')
                                ->with('notes')
                                ->orderByDesc('notes_count')
                                ->get();
                                
       if ($tasksWithNotes->isEmpty()) {
            return $this->sendResponse([], 'NO Tasks found!!');
       }
       return $this->sendResponse(TaskResource::collection($tasksWithNotes),'Task retrived successfully.');
    } 
}
