<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    use HasFactory;

    // On autorise Laravel à remplir ces colonnes (Mass Assignment)
    protected $fillable = [
        'subject', 
        'day', 
        'room', 
        'start_time', 
        'end_time', 
        'class_name',
        'status' // <-- INDISPENSABLE pour la cohérence avec le module suivant
    ];
}