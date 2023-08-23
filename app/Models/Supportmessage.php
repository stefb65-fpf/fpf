<?php

namespace App\Models;

use App\Concern\DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supportmessage extends Model
{
    use HasFactory;
    protected $guarded = [];
    use DateTime;

}
