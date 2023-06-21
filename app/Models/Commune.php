<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commune extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function departement() {
        return $this->belongsTo('App\Models\Departement');
    }
}
