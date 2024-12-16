<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{

    protected $hidden = ['id'];

    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'document',
    ];
}
