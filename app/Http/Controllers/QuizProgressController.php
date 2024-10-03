<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserQuizProgress;

class QuizProgressController extends Controller
{
    //
    public function index()
    {
        // Fetch all rows from the user_quiz_progress table
        $quizProgress = UserQuizProgress::all();

        // Pass the data to the view
        return view('quiz_progress', compact('quizProgress'));
    }
}
