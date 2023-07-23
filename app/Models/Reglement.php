<?php

namespace App\Models;

use App\Concern\DateTime;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reglement extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function utilisateurs() {
        return $this->belongsToMany('App\Models\Utilisateur', 'reglementsutilisateurs', 'reglements_id', 'utilisateurs_id');
    }

    use DateTime;

    public function getDateenregistrementAttribute($value) {
        if ($value == null) return '';
        return Carbon::createFromFormat('Y-m-d H:i:s', $value)->format('d/m/Y H:i');
    }
}
