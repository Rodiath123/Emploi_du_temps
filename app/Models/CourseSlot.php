<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CourseSlot extends Model
{
    use HasFactory;

    // On liste les colonnes que Laravel peut remplir
    protected $fillable = [
        'class_id',
        'subject_id',
        'teacher_id',
        'room_id',
        'day',
        'start_time',
        'end_time',
        'status',
    ];
}