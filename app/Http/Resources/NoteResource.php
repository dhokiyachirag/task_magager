<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class NoteResource extends JsonResource
{
    public function toArray($request)
    {
        $attachments = $this->attachments;
        $attachments = array_map(function ($filename) {
                            return url('storage/'.$filename);
                        }, $attachments);

        return [
            'id' => $this->id,
            'subject' => $this->subject,
            'note' => $this->note,
            'attachments' => $attachments,
        ];
    }
}
