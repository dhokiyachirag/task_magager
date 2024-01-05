<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    use HasFactory;

    protected $fillable = ['subject', 'note', 'attachments'];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }
    public function getAttachmentsAttribute($value)
    {
        return json_decode($value, true);
    }

    // Mutator for attachments to convert array to JSON
    public function setAttachmentsAttribute($value)
    {
        $this->attributes['attachments'] = json_encode($value);
    }
}
