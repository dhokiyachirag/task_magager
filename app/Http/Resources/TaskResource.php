<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray($request)
    {
        return [
             'id' => $this->id,
            'subject' => $this->subject,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'due_date' => $this->due_date,
            'status' => $this->status,
            'priority' => $this->priority,
            'note_count' => $this->notes_count,
            'notes' => NoteResource::collection($this->whenLoaded('notes')),
        ];
    }
}
