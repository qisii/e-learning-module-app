<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Question;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;

class QuizzesController extends Controller
{
    private $quiz;
    private $folder;
    private $question;
    private $quiz_attempt;

    public function __construct(Quiz $quiz, Folder $folder, Question $question, QuizAttempt $quiz_attempt)
    {
        $this->quiz     = $quiz;
        $this->folder   = $folder;
        $this->question = $question;
        $this->quiz_attempt = $quiz_attempt;
    }

    // public function showPretest($folder_id){
    //     $folder = $this->folder->findOrFail($folder_id);
    //     if ($folder->folder_type_id != 1){
    //         return redirect()->route('admin.projects');
    //     }

    //     return view('admin.projects.quizzes.pretest.show')
    //             ->with('folder', $folder);
    // }

    public function showPretest($folder_id)
    {
        $folder = $this->folder->findOrFail($folder_id);

        if ($folder->folder_type_id != 1) {
            return redirect()->route('admin.projects');
        }

        // Check if the folder already has a quiz (assuming 1 quiz per folder)
        $quiz = $folder->quizzes()->first(); // or ->latest()->first() if multiple

         return view('admin.projects.quizzes.pretest.show')
                ->with('folder', $folder)
                ->with('quiz', $quiz);
    }

    public function showModule($folder_id){
        $folder = $this->folder->findOrFail($folder_id);
        $level_id = request('level_id');
        if ($folder->folder_type_id != 2){
            return redirect()->route('admin.projects');
        }

        return view('admin.projects.modules.show')
                ->with('folder', $folder)
                ->with('level_id', $level_id);
    }

    public function deleteQuestion($question_id){
        $this->question->destroy($question_id);

        return redirect()->back();
    }

    public function refresh(){
        return redirect()->back();
    }


    // ---------- POST TEST ----------
    public function showPostTest($folder_id)
    {
        $folder = $this->folder->findOrFail($folder_id);

        if ($folder->folder_type_id != 3) {
            return redirect()->route('admin.projects');
        }

        // Check if the folder already has a quiz (assuming 1 quiz per folder)
        $quiz = $folder->quizzes()->first(); // or ->latest()->first() if multiple

         return view('admin.projects.quizzes.post-tests.show')
                ->with('folder', $folder)
                ->with('quiz', $quiz);
    }
}
