<?php

namespace App\Models;

use App\Concern\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Historiquemail extends Model
{
    use HasFactory;
    protected $guarded = [];
    use DateTime;

    public function personne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
