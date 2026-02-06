<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Creneau extends Model
{
    use HasFactory;

    protected $fillable = ['matiere_id', 'salle_id', 'jour', 'heure_debut', 'heure_fin'];
    
    public function matiere()
    {
        return $this->belongsTo(Matiere::class);
    }
    
    public function salle()
    {
        return $this->belongsTo(Salle::class);
    }
}
