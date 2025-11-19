<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Session extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function formation() {
        return $this->belongsTo('App\Models\Formation');
    }

    public function evaluations() {
        return $this->hasMany('App\Models\Evaluation');
    }

    public function inscrits() {
        return $this->hasMany('App\Models\Inscrit');
    }

    public function club()
    {
        return $this->belongsTo('App\Models\Club');
    }

    public function ur()
    {
        return $this->belongsTo('App\Models\Ur');
    }
}
