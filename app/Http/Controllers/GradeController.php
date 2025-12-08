<?php

namespace App\Http\Controllers;

use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradeController extends Controller
{
    public $quiz_attempt;

    public function __construct(QuizAttempt $quiz_attempt)
    {
        $this->quiz_attempt = $quiz_attempt;
    }

    // public function index(){
    //     $all_grades = $this->quiz_attempt->where('user_id', Auth::user()->id)->latest()->paginate(8);
    //     $pretests = $all_grades->where('quiz.folder.folder_type_id', 1);
    //     $posttests = $all_grades->where('quiz.folder.folder_type_id', 3);

    //     return view('users.grades.index')
    //         ->with('all_grades', $all_grades)
    //         ->with('pretests', $pretests)
    //         ->with('posttests', $posttests);
    // }

    // Pretest Grades
    public function indexPretest()
    {
        $pretests = QuizAttempt::with('quiz.folder.project', 'quiz.questions')
            ->where('user_id', Auth::id())
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 1); // Pretest
            })
            ->latest()
            ->paginate(8);

        return view('users.grades.index')->with('pretests', $pretests);
    }

    // Post test Grades
    public function indexPosttest()
    {
        $posttests = QuizAttempt::with('quiz.folder.project', 'quiz.questions')
            ->where('user_id', Auth::id())
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 3); // Post Test
            })
            ->latest()
            ->paginate(8);

        return view('users.grades.post-test')->with('posttests', $posttests);
    }

    public function deleteOldAttempts()
    {
        $cutoffDate = Carbon::now()->subMonths(3);

        QuizAttempt::where('created_at', '<', $cutoffDate)->delete();

        return redirect()->back()->with('success', 'Old quiz attempts deleted successfully.');
    }

}
