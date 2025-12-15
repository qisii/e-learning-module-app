<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Handout;
use App\Models\HandoutAttempt;
use App\Models\Project;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class AnalysisData extends Component
{
    public $genderFilter = '';
    public $gradeFilter = '';
    public $sectionFilter = '';
    public $projects = [];
    public $projectId;
    public $startAttempt;
    public $projectIdModule;

    protected $listeners = ['refreshCharts' => '$refresh'];

    public function updated($property)
    {
        $this->dispatchChartsUpdate();
    }

    public function mount()
    {
        $this->loadUserProjects();
        $this->dispatchChartsUpdate();
    }

    public function dispatchChartsUpdate()
    {
        $stats = $this->studentStats;
        logger()->info('Student stats for charts', $stats);

        $this->dispatch('update-charts', [
            'genderData' => [
                'male' => $stats['male'],
                'female' => $stats['female'],
                'other' => $stats['other'],
                'none' => $stats['none'],
            ],
            'totalData' => [
                'male' => $stats['male'],
                'female' => $stats['female'],
                'other' => $stats['other'],
                'none' => $stats['none'],
            ],
        ]);
    }

    public function getStudentStatsProperty()
    {
        $query = User::where('role_id', 2);

        if ($this->genderFilter) $query->where('gender', $this->genderFilter);
        if ($this->gradeFilter) $query->where('grade_level', $this->gradeFilter);
        if ($this->sectionFilter) $query->where('section', $this->sectionFilter);

        return [
            'total'  => (clone $query)->count(),
            'male'   => (clone $query)->where('gender', 'male')->count(),
            'female' => (clone $query)->where('gender', 'female')->count(),
            'other'  => (clone $query)->where('gender', 'other')->count(),
            'none'   => (clone $query)->whereNull('gender')->orWhere('gender', '')->count(),
        ];
    }

    public function getLearningStatsProperty()
    {
        // Average Scores
        $avgPretest = QuizAttempt::whereHas('quiz.folder', function ($q) {
            $q->where('folder_type_id', 1); // Pretest
        })->avg('score');

        $avgPosttest = QuizAttempt::whereHas('quiz.folder', function ($q) {
            $q->where('folder_type_id', 3); // Post-test
        })->avg('score');
        
        // Module Completion rate
        $totalStudents = User::where('role_id', 2)->count();

        // Users who completed all 3 levels
        $studentsWithModuleAttempt = User::where('role_id', 2)
            ->whereHas('handoutAttempts', function ($q) {
                $q->whereHas('handout', fn($q2) => $q2->where('level_id', 1));
            })
            ->whereHas('handoutAttempts', function ($q) {
                $q->whereHas('handout', fn($q2) => $q2->where('level_id', 2));
            })
            ->whereHas('handoutAttempts', function ($q) {
                $q->whereHas('handout', fn($q2) => $q2->where('level_id', 3));
            })
            ->count();

        $moduleCompletionRate = $totalStudents > 0
            ? round(($studentsWithModuleAttempt / $totalStudents) * 100, 1)
            : 0;

        return [
            'avg_pretest'        => round($avgPretest ?? 0, 1),
            'avg_posttest'       => round($avgPosttest ?? 0, 1),
            'module_completion'  => $moduleCompletionRate,
        ];
    }

    public function getTopScorersProperty()
    {
        $authUserId = Auth::user()->id;

        // Get all projects created by the auth user
        $projects = Project::where('user_id', $authUserId)->pluck('id');

        if ($projects->isEmpty()) {
            return [
                'pretest' => collect(),
                'posttest' => collect(),
            ];
        }

        // Top 5 Pretest scorers
        $topPretest = QuizAttempt::with('user', 'quiz.folder.project')
            ->whereHas('quiz.folder', fn($q) => $q->whereIn('project_id', $projects))
            ->whereHas('quiz.folder', fn($q) => $q->where('folder_type_id', 1)) // Pretest
            ->orderByDesc('score')
            ->take(5)
            ->get();

        // Top 5 Post-test scorers
        $topPosttest = QuizAttempt::with('user', 'quiz.folder.project')
            ->whereHas('quiz.folder', fn($q) => $q->whereIn('project_id', $projects))
            ->whereHas('quiz.folder', fn($q) => $q->where('folder_type_id', 3)) // Post-test
            ->orderByDesc('score')
            ->take(5)
            ->get();

        return [
            'pretest' => $topPretest,
            'posttest' => $topPosttest,
        ];
    }

    public function loadUserProjects()
    {
        $this->projects = Project::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function getDateTimeProperty()
    {
        $now = Carbon::now('Asia/Manila');
        $isDay = $now->hour >= 6 && $now->hour < 18;

        return [
            'icon' => $isDay ? '☀️' : '🌙',
            'text' => $now->format('l, F j Y • h:i A'),
        ];
    }

    public function filterPrePost()
    {
        $this->validate([
            'projectId'    => 'required|exists:projects,id',
            'startAttempt' => 'required|integer|min:1',
        ]);

        $endAttempt = $this->startAttempt + 4; // 5-attempt interval

        // 1️⃣ Get pretest & post-test folders for the project
        $pretestFolderIds  = Folder::where('project_id', $this->projectId)
                                ->where('folder_type_id', 1)
                                ->pluck('id');
        
        $posttestFolderIds = Folder::where('project_id', $this->projectId)
                                ->where('folder_type_id', 3)
                                ->pluck('id');

        // 2️⃣ Get quizzes in those folders
        $pretestQuizIds  = Quiz::whereIn('folder_id', $pretestFolderIds)->pluck('id');
        $posttestQuizIds = Quiz::whereIn('folder_id', $posttestFolderIds)->pluck('id');

        // 3️⃣ Get quiz attempts within the attempt range
        $pretestAttempts  = QuizAttempt::with('user', 'quiz')
            ->whereIn('quiz_id', $pretestQuizIds)
            ->whereBetween('attempt_number', [$this->startAttempt, $endAttempt])
            ->get();

        $posttestAttempts = QuizAttempt::with('user', 'quiz')
            ->whereIn('quiz_id', $posttestQuizIds)
            ->whereBetween('attempt_number', [$this->startAttempt, $endAttempt])
            ->get();

        // 4️⃣ Structure pretest data with additional user info & created_at
        $pretestData = collect(range($this->startAttempt, $endAttempt))
            ->mapWithKeys(fn($attemptNumber) => [
                $attemptNumber => [
                    'attemptNumber' => $attemptNumber,
                    'totalScore' => $pretestAttempts->where('attempt_number', $attemptNumber)->sum('score'),
                    'users' => $pretestAttempts->where('attempt_number', $attemptNumber)
                        ->map(fn($a) => [
                            'user'        => $a->user->first_name . ' ' . $a->user->last_name,
                            'username'    => $a->user->username ?? null,
                            'grade_level' => $a->user->grade_level ?? null,
                            'section'     => $a->user->section ?? null,
                            'score'       => $a->score,
                            'created_at'  => $a->created_at->toDateTimeString(), // when user took the quiz
                        ])->values(),
                ]
            ]);

        // 5️⃣ Structure post-test data with additional info
        $posttestData = collect(range($this->startAttempt, $endAttempt))
            ->mapWithKeys(fn($attemptNumber) => [
                $attemptNumber => [
                    'attemptNumber' => $attemptNumber,
                    'totalScore' => $posttestAttempts->where('attempt_number', $attemptNumber)->sum('score'),
                    'users' => $posttestAttempts->where('attempt_number', $attemptNumber)
                        ->map(fn($a) => [
                            'user'        => $a->user->first_name . ' ' . $a->user->last_name,
                            'username'    => $a->user->username ?? null,
                            'grade_level' => $a->user->grade_level ?? null,
                            'section'     => $a->user->section ?? null,
                            'score'       => $a->score,
                            'created_at'  => $a->created_at->toDateTimeString(),
                        ])->values(),
                ]
            ]);

        // 6️⃣ Dispatch to frontend
        $this->dispatch('update-charts', [
            'prePostScores' => [
                'pretest' => $pretestData,
                'posttest' => $posttestData,
            ],
            'startAttempt' => $this->startAttempt,
            'endAttempt'   => $endAttempt,
            'projectId'    => $this->projectId,
        ]);
    }

    public function filterModule()
    {
        $this->validate([
            'projectIdModule' => 'required|exists:projects,id',
        ]);

        // 1️⃣ Get module folders for the project
        $moduleFolders = Folder::where('project_id', $this->projectIdModule)
                            ->where('folder_type_id', 2)
                            ->get();

        if ($moduleFolders->isEmpty()) {
            $this->dispatch('update-charts', [
                'moduleAttempts' => [],
                'totalAttemptsPerLevel' => [],
                'projectId' => $this->projectIdModule,
            ]);
            return;
        }

        // 2️⃣ Get all handouts in those folders
        $handouts = Handout::whereIn('folder_id', $moduleFolders->pluck('id'))->get();

        if ($handouts->isEmpty()) {
            $this->dispatch('update-charts', [
                'moduleAttempts' => [],
                'totalAttemptsPerLevel' => [],
                'projectId' => $this->projectIdModule,
            ]);
            return;
        }

        // 3️⃣ Get all handout attempts for these handouts
        $handoutAttempts = HandoutAttempt::with('user', 'handout')
            ->whereIn('handout_id', $handouts->pluck('id'))
            ->get();

        // 4️⃣ Prepare structured data: group by level and then by user
        $data = $handoutAttempts->groupBy(fn($attempt) => $attempt->handout->level_id)
            ->map(function($levelAttempts, $levelId) {
                $users = $levelAttempts->groupBy('user_id')->map(function($userAttempts) {
                    $firstAttempt = $userAttempts->first();
                    return [
                        'user'        => $firstAttempt->user->first_name . ' ' . $firstAttempt->user->last_name,
                        'username'    => $firstAttempt->user->username ?? null,
                        'grade_level' => $firstAttempt->user->grade_level ?? null,
                        'section'     => $firstAttempt->user->section ?? null,
                        'totalAttempts' => $userAttempts->sum('attempt_number'),
                        'created_at'    => $firstAttempt->created_at->toDateTimeString(), // first attempt timestamp
                    ];
                })->values();

                // Total attempts for this level (all users)
                $totalAttempts = $levelAttempts->sum('attempt_number');

                return [
                    'users' => $users,
                    'totalAttempts' => $totalAttempts,
                ];
            });

        // 5️⃣ Dispatch data to frontend
        $this->dispatch('update-charts', [
            'moduleAttempts' => $data, // structured by level_id, includes totalAttempts and user info
            'projectId'      => $this->projectIdModule,
        ]);
    }

    public function render()
    {
        // $this->dispatchChartsUpdate();
        return view('livewire.analysis-data');
    }
}
