<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserQuizProgress extends Model
{
    protected $table = 'user_quiz_progress';

    protected $fillable = ['user_id', 'question_number', 'score'];

    public $timestamps = true;
}
