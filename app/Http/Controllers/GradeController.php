<?php

namespace App\Http\Controllers;

use App\Models\HandoutAttempt;
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

    public function searchPretest(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date'); // NEW

        $pretests = QuizAttempt::with('quiz.folder.project', 'quiz.questions')
            ->where('user_id', Auth::id())
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 1); // Pretest
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Project title
                    $q->whereHas('quiz.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    // Score
                    ->orWhere('score', 'like', "%$search%")

                    // Attempt number
                    ->orWhere('attempt_number', 'like', "%$search%");

                    // Time spent in mm:ss format
                    if (preg_match('/^(\d+):(\d+)$/', $search, $matches)) {
                        $seconds = ($matches[1] * 60) + $matches[2];
                        $q->orWhere('time_spent', $seconds);
                    }
                });
            })
            ->when($date, function ($query) use ($date) {  // NEW DATE FILTER
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(8)
            ->appends([
                'search' => $search,
                'date'   => $date,
            ]);

        return view('users.grades.index', compact('pretests'));
    }

    public function searchPosttest(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date'); // NEW

        $posttests = QuizAttempt::with('quiz.folder.project', 'quiz.questions')
            ->where('user_id', Auth::id())
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 3); // Post-test
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // Project title
                    $q->whereHas('quiz.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    // Score
                    ->orWhere('score', 'like', "%$search%")

                    // Attempt number
                    ->orWhere('attempt_number', 'like', "%$search%");

                    // Time spent in mm:ss format
                    if (preg_match('/^(\d+):(\d+)$/', $search, $matches)) {
                        $seconds = ($matches[1] * 60) + $matches[2];
                        $q->orWhere('time_spent', $seconds);
                    }
                });
            })
            ->when($date, function ($query) use ($date) { // NEW DATE FILTER
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(8)
            ->appends([
                'search' => $search,
                'date'   => $date,
            ]);

        return view('users.grades.post-test', compact('posttests'));
    }




    // ---------- ADMIN SIDE ----------
    public function indexPretestAdmin()
    {
        $pretests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 1); // Pretest
            })
            ->whereHas('quiz.folder.project', function ($query) {
                $query->where('user_id', Auth::id()); // Projects created by this admin
            })
            ->latest()
            ->paginate(8);

        return view('admin.grades.index')->with('pretests', $pretests);
    }

    public function indexPosttestAdmin()
    {
        $posttests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 3); // Post Test
            })
            ->whereHas('quiz.folder.project', function ($query) {
                $query->where('user_id', Auth::id()); // Projects created by this admin
            })
            ->latest()
            ->paginate(8);

        return view('admin.grades.post-test')->with('posttests', $posttests);
    }

    public function searchPretestAdmin(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date'); // NEW

        $pretests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 1); // Pretest
            })
            ->whereHas('quiz.folder.project', function ($query) {
                $query->where('user_id', Auth::id()); // Projects by admin
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // User filters
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('grade_level', 'like', "%$search%")
                        ->orWhere('section', 'like', "%$search%");
                    })

                    // Project title
                    ->orWhereHas('quiz.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    // Score
                    ->orWhere('score', 'like', "%$search%")

                    // Attempt number
                    ->orWhere('attempt_number', 'like', "%$search%");

                    // Time spent (mm:ss format)
                    if (preg_match('/^(\d+):(\d+)$/', $search, $matches)) {
                        $seconds = ($matches[1] * 60) + $matches[2];
                        $q->orWhere('time_spent', $seconds);
                    }
                });
            })
            ->when($date, function ($query) use ($date) { // NEW DATE FILTER
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(8)
            ->appends([
                'search' => $search,
                'date'   => $date, // Keep date in pagination
            ]);

        return view('admin.grades.index')->with('pretests', $pretests);
    }

    public function searchPostTestAdmin(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date'); // NEW

        $posttests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', function ($query) {
                $query->where('folder_type_id', 3); // Post-test
            })
            ->whereHas('quiz.folder.project', function ($query) {
                $query->where('user_id', Auth::id());
            })
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    // User filters
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('grade_level', 'like', "%$search%")
                        ->orWhere('section', 'like', "%$search%");
                    })

                    // Project title
                    ->orWhereHas('quiz.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    // Score
                    ->orWhere('score', 'like', "%$search%")

                    // Attempt number
                    ->orWhere('attempt_number', 'like', "%$search%");

                    // Time spent (mm:ss format)
                    if (preg_match('/^(\d+):(\d+)$/', $search, $matches)) {
                        $seconds = ($matches[1] * 60) + $matches[2];
                        $q->orWhere('time_spent', $seconds);
                    }
                });
            })
            ->when($date, function ($query) use ($date) { // NEW DATE FILTER
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(8)
            ->appends([
                'search' => $search,
                'date'   => $date, // Keep date in pagination
            ]);

        return view('admin.grades.post-test')->with('posttests', $posttests);
    }

    public function indexModuleAdmin(Request $request)
    {
        // Get all handout attempts for handouts belonging to projects created by the admin
        $search = $request->input('search');

        $modules = HandoutAttempt::with(['user', 'handout.folder.project'])
            ->whereHas('handout.folder.project', function ($query) {
                $query->where('user_id', Auth::id()); // Only projects created by the admin
            })
            ->when($search, function ($query, $search) {
                $query->whereHas('user', function ($q) use ($search) {
                    $q->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('grade_level', 'like', "%{$search}%")
                    ->orWhere('section', 'like', "%{$search}%");
                })
                ->orWhereHas('handout.folder.project', function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%");
                })
                ->orWhere('attempt_number', 'like', "%{$search}%")
                ->orWhere('time_spent', 'like', "%{$search}%");
            })
            ->latest()
            ->paginate(8)
            ->appends(['search' => $search]); // Keep search query on pagination

        return view('admin.grades.module')->with('modules', $modules);
    }
    
    public function searchModuleAdmin(Request $request)
    {
        $search = $request->input('search');
        $date = $request->input('date'); // NEW

        // Map level names to IDs
        $levelMap = [
            'easy' => 1,
            'average' => 2,
            'hard' => 3,
        ];

        $searchLower = strtolower($search);
        $searchLevelId = $levelMap[$searchLower] ?? null;

        $modules = HandoutAttempt::with('user', 'handout.folder.project')
            ->whereHas('handout.folder.project', function ($q) {
                $q->where('user_id', Auth::id()); // Projects created by this admin
            })
            ->when($search, function ($query) use ($search, $searchLevelId) {
                $query->where(function ($q) use ($search, $searchLevelId) {

                    // User filters
                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%")
                        ->orWhere('grade_level', 'like', "%$search%")
                        ->orWhere('section', 'like', "%$search%");
                    })

                    // Project title
                    ->orWhereHas('handout.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    // Level filter
                    ->orWhere(function ($lvl) use ($searchLevelId) {
                        if ($searchLevelId) {
                            $lvl->whereHas('handout', function ($h) use ($searchLevelId) {
                                $h->where('level_id', $searchLevelId);
                            });
                        }
                    })

                    // Attempt number
                    ->orWhere('attempt_number', 'like', "%$search%");

                    // Time spent in mm:ss format
                    if (preg_match('/^(\d+):(\d+)$/', $search, $matches)) {
                        $seconds = ($matches[1] * 60) + $matches[2];
                        $q->orWhere('time_spent', $seconds);
                    }

                });
            })
            // Date filter
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(8)
            ->appends([
                'search' => $search,
                'date'   => $date,
            ]);

        return view('admin.grades.module')->with('modules', $modules);
    }

}