<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = false;

    public function personnes() {
        return $this->belongsToMany('App\Models\Personne');
    }

    public function getTelephonedomicileAttribute($value){
        $isPoint = strpos($value, ".");
        $number = substr($value, strpos($value, ".") + 1);
        $number = "0".$number;
        $splitted_number = trim(chunk_split($number, 2, ' '));
        return $isPoint? $splitted_number :$value;
    }

    public function clubs() {
        return $this->hasMany('App\Models\Club');
    }
}
