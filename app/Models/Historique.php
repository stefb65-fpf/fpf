<?php

namespace App\Models;

use App\Concern\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historique extends Model
{
    use HasFactory;
    protected $guarded = [];
    use DateTime;

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
