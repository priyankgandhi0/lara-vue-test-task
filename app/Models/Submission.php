<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $table = 'submissions';

    protected $fillable = [
        'name',
        'email',
        'phone',
        'message',
        'street',
        'state',
        'zip',
        'country',
    ];
}