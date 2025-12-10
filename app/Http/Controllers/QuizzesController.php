<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Handout;
use App\Models\HandoutPage;
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

    // public function showModule($folder_id){
    //     $folder = $this->folder->findOrFail($folder_id);
    //     $level_id = request('level_id');
    //     if ($folder->folder_type_id != 2){
    //         return redirect()->route('admin.projects');
    //     }

    //     return view('admin.projects.modules.show')
    //             ->with('folder', $folder)
    //             ->with('level_id', $level_id);
    // }

    public function showModule($folder_id)
{
    $level_id = request('level_id');

    try {
        $folder = $this->folder->findOrFail($folder_id);

        // If folder type is not 2, redirect
        if ($folder->folder_type_id != 2) {
            return redirect()->route('admin.projects');
        }
    } catch (\Exception $e) {
        // Ignore errors, optionally log them
        // \Log::error($e->getMessage());

        // Provide a fallback folder object so the view can still render
        $folder = (object)[
            'id' => $folder_id,
            'folder_type_id' => null,
            'name' => 'Unknown folder'
        ];
    }

    // Always render the view
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

    public function previewModule($folder_id)
    {
        $folder = Folder::where('id', $folder_id)
            ->where('folder_type_id', 2)
            ->first();

        if (! $folder) {
            abort(404, 'Module folder not found');
        }

        // 2. Find the handout for the given level
        $level_id = request('level_id');
        $handout = Handout::where('folder_id', $folder->id)
            ->where('level_id', $level_id)
            ->first();

        if (! $handout) {
            abort(404, 'Handout not found');
        }

        // 3. Get the pages with components
        $pages = HandoutPage::where('handout_id', $handout->id)
            ->with(['components' => function ($q) {
                $q->orderBy('sort_order');
            }])
            ->orderBy('page_number')
            ->paginate(1)
            ->appends(['level_id' => $level_id]);

        return view('admin.projects.modules.preview', compact(
            'folder',
            'level_id',
            'handout',
            'pages'
        ));
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
