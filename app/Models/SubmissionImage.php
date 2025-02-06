<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmissionImage extends Model
{
    protected $table = 'submission_images';

    protected $fillable = [
        'submission_id',
        'image',
    ];
}