<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

// app/Models/Especie.php
class Especie extends Model
{
    protected $table = 'especie'; 
    protected $fillable = ['nome'];
}