<?php
<?php

namespace App\Http\Controllers\Shared;

use Illuminate\Http\Request;

class PetPersonalityQuizController extends Controller
{
    public function showQuiz()
    {
    $answers = $request->only([
        'question1', 'question2', 'question3', 'question4', 'question5'
    ]);

    return redirect()->route('adopter.pet-swipe', $answers); // query string filter
}
}