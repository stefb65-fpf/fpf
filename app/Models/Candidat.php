<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Candidat extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function elections() {
        return $this->belongsTo('App\Models\Election');
    }
    public function utilisateur() {
        return $this->belongsTo('App\Models\Utilisateur', 'utilisateurs_id');
    }
}
