<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Carbon\Carbon;
class Personne extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function adresses() {
        return $this->belongsToMany('App\Models\Adresse');
    }

    public function utilisateurs() {
        return $this->hasMany('App\Models\Utilisateur');
    }

    public function abonnements() {
        return $this->hasMany('App\Models\Abonnement');
    }

    public function historiques() {
        return $this->hasMany('App\Models\Historique');
    }

    public function historiquemails() {
        return $this->hasMany('App\Models\Historiquemail');
    }
//    public function getDatenaissanceAttribute($value){
//        return $value? Carbon::createFromFormat('Y-m-d', $value)->format('d/m/Y'):null;
//    }
    public function getPhoneMobileAttribute($value){
        $isPoint = strpos($value, ".");
        $number = substr($value, strpos($value, ".") + 1);
        $number = "0".$number;
        $splitted_number = trim(chunk_split($number, 2, ' '));
        return $isPoint ? $splitted_number : $value;
    }
}
