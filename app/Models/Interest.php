<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Interest extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function formation() {
        return $this->belongsTo('App\Models\Formation');
    }

    public function pesonne() {
        return $this->belongsTo('App\Models\Personne');
    }
}
