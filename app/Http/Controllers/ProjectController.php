<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Handout;
use App\Models\HandoutAttempt;
use App\Models\HandoutPage;
use App\Models\PdfResource;
use App\Models\Project;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    private $project;
    private $folder;
    private $quiz;
    private $quiz_attempt;
    private $handout;
    private $handout_attempt;

    public function __construct(Project $project, Folder $folder, Quiz $quiz, QuizAttempt $quiz_attempt, Handout $handout, HandoutAttempt $handout_attempt)
    {
        $this->project  = $project;
        $this->folder   = $folder;
        $this->quiz     = $quiz;
        $this->quiz_attempt = $quiz_attempt;
        $this->handout = $handout;
        $this->handout_attempt = $handout_attempt;
    }

    public function index()
    {
        // Safely clear all custom session data EXCEPT AUTH USER
        $this->removeSession();

        // Get only projects that have 3 specific folders: 1, 2, and 3
        $all_projects = $this->project
            ->whereHas('folders', function ($query) {
                $query->whereIn('folder_type_id', [1, 2, 3]);
            })
            ->withCount(['folders' => function ($query) {
                $query->whereIn('folder_type_id', [1, 2, 3]);
            }])
            ->having('folders_count', 3) // must have all three folder types
            ->with(['folders' => function ($query) {
                $query->orderBy('folder_type_id', 'asc');
            }])
            ->latest()
            ->paginate(6);

        return view('users.projects.index')
            ->with('all_projects', $all_projects);
    }

    protected function removeSession()
    {
        // All current keys
        $allKeys = array_keys(session()->all());

        // Base protected keys (Laravel internals + csrf)
        $protectedKeys = [
            '_token',
            '_previous',
            '_flash',
            '_flash.old',
            '_flash.new',
            '_csrf_token', // if present in some apps
        ];

        // Protect auth keys like login_web_xxx or login_??? (covers common guards)
        foreach ($allKeys as $key) {
            if (str_starts_with($key, 'login_')) {
                $protectedKeys[] = $key;
            }
        }

        // Also protect any currently-flashed keys (so flash messages survive)
        $flashNew = session()->get('_flash.new', []);
        $flashOld = session()->get('_flash.old', []);

        $flashKeys = array_unique(array_merge(
            is_array($flashNew) ? $flashNew : [],
            is_array($flashOld) ? $flashOld : []
        ));

        foreach ($flashKeys as $fKey) {
            $protectedKeys[] = $fKey;
        }

        // Include custom keys you want to clear when exiting quiz
        // For example, 'quiz_answers' and 'quiz_timer' to reset the quiz
        $keysToForget = array_diff($allKeys, $protectedKeys);

        // Remove the quiz sessions specifically
        if (session()->has('quiz_answers')) {
            $keysToForget[] = 'quiz_answers';
        }
        if (session()->has('quiz_timer')) {
            $keysToForget[] = 'quiz_timer';
        }

        // Deduplicate
        $keysToForget = array_unique($keysToForget);

        // Forget all non-protected keys
        session()->forget($keysToForget);
    }

    // ---------- PRE TEST ----------
    public function welcomePretest($project_id)
    {
        $project = $this->project->findOrFail($project_id);
        $pretestFolder = $this->getFolderByType($project, 1);
        
        $quizIds = Quiz::where('folder_id', $pretestFolder->id)->pluck('id');
        $latestAttempt = QuizAttempt::whereIn('quiz_id', $quizIds)
            ->where('user_id', Auth::id())
            ->latest()
            ->first();
        
        $attempted = false;

        if ($latestAttempt) {
            $weekAgo = now()->subDays(7);

            // If attempt is WITHIN last 7 days → show score
            if ($latestAttempt->created_at->gte($weekAgo)) {
                $attempted = true;
                return $this->openPreScore($latestAttempt->id, $attempted);
            }
        }

        // Otherwise show pretest page
        return view('users.projects.pretest.index')
            ->with('project', $project)
            ->with('pretestFolder', $pretestFolder)
            ->with('attempted', $attempted);
    }

    public function showPretest($project_id){
        // Get project
        $project = $this->project->findOrFail($project_id);

        // Get the folder with folder_type_id = 1 (Pretest)
        $pretestFolder = $this->getFolderByType($project, 1);

        // Get one quiz of the pretest folder
        $pretest = $pretestFolder->quizzes()->first();

        // Find the pdf for this quiz
        $gdriveLink = PdfResource::where('quiz_id', $pretest->id)
                    ->whereNull('handout_id')   // only quiz PDFs
                    ->value('gdrive_link');

        // dd($pretestFolder);
        
        return view('users.projects.pretest.show')
                ->with('project', $project)
                ->with('pretest', $pretest)
                ->with('gdriveLink', $gdriveLink);
    }

    private function getFolderByType($project, $folderTypeId)
    {
        return $project->folders()
            ->where('folder_type_id', $folderTypeId)
            ->first();
    }

    public function openPreScore($quiz_attempt_id, $attempted = null){
        $quiz_attempt = $this->quiz_attempt->findOrFail($quiz_attempt_id);
        $project = $quiz_attempt->quiz->folder->project;
        return view('users.projects.pretest.score')
                ->with('quiz_attempt', $quiz_attempt)
                ->with('project', $project)
                ->with('attempted', $attempted);
    }

    public function openPostScore($quiz_attempt_id){
        $quiz_attempt = $this->quiz_attempt->findOrFail($quiz_attempt_id);
        $project = $quiz_attempt->quiz->folder->project;
        return view('users.projects.post-test.score')
                ->with('quiz_attempt', $quiz_attempt)
                ->with('project', $project);
    }

    // ---------- POST TEST ----------
    public function welcomePostTest($project_id){
        $project = $this->project->findOrFail($project_id);

        // Get the Post-test folder (folder_type_id = 3)
        $postTestFolder = $this->getFolderByType($project, 3);
        
        return view('users.projects.post-test.index')
                ->with('project', $project)
                ->with('postTestFolder', $postTestFolder);
    }

    public function showPostTest($project_id){
        // Get project
        $project = $this->project->findOrFail($project_id);

        // Get the folder with folder_type_id = 3 (Post-test)
        $postTestFolder = $this->getFolderByType($project, 3);

        // Get one quiz of the post-test folder
        $post_test = $postTestFolder->quizzes()->first();

        // Find the pdf for this quiz
        $gdriveLink = PdfResource::where('quiz_id', $post_test->id)
                    ->whereNull('handout_id')   // only quiz PDFs
                    ->value('gdrive_link');

        // dd($postTestFolder);
        
        return view('users.projects.post-test.show')
                ->with('project', $project)
                ->with('post_test', $post_test);
    }

    // ---------- MODULE HANDOUT ----------
    // public function showModule($project_id, $level_id){
    //     // get the module based on the level and get all the handout_components.
    //     // get the module using project, get the folder with folder_type_id is 2
    //     // then get the handout of that folder then check the level_id 
    //     // get all the handout_pages of the module handout
    //     // get all the handout_components
    //     // use paginate(1)

    //     return view('users.projects.modules.show')
    //             ->with('project_id', $project_id)
    //             ->with('level_id', $level_id);
    // }

    public function showModule($project_id, $level_id)
    {
        // 1. Find the module folder (folder_type_id = 2)
        $folder = Folder::where('project_id', $project_id)
            ->where('folder_type_id', 2)
            ->first();

        if (! $folder) {
            abort(404, 'Module folder not found');
        }

        // 2. Find the handout for the given level
        $handout = Handout::where('folder_id', $folder->id)
            ->where('level_id', $level_id)
            ->first();

        // Get Google Drive PDF link for this handout (nullable)
        $gdriveLink = $handout
                    ? PdfResource::where('handout_id', $handout->id)
                        ->whereNull('quiz_id') // only handout PDFs
                        ->value('gdrive_link')
                    : null;

        // 3. Get the pages with components
        $pages = $handout
                ? HandoutPage::where('handout_id', $handout->id)
                    ->with(['components' => function ($q) {
                        $q->orderBy('sort_order');
                    }])
                    ->orderBy('page_number')
                    ->paginate(1)
                : collect();

        return view('users.projects.modules.show', compact(
            'project_id',
            'level_id',
            'handout',
            'pages',
            'gdriveLink'
        ));
    }

   public function storeHandoutAttempt(Request $request, $handout_id)
    {
        $userId = Auth::user()->id;
        $handout = $this->handout->findOrFail($handout_id);
        $levelId = $handout->level_id;
        $seconds = $request->input('seconds', 0);
        $attemptNumber = $this->handout_attempt->where('user_id', $userId)
                            ->where('handout_id', $handout_id)
                            ->count() + 1;

        // Save attempt
        $this->handout_attempt->create([
            'user_id' => $userId,
            'handout_id' => $handout_id,
            'level_id' => $levelId,
            'time_spent' => $seconds,
            'attempt_number' => $attemptNumber,
        ]);
        
        // Redirect to the next page passed from the form
        $nextPage = $request->input('next_page');
        return redirect($nextPage);
    }

}
