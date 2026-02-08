<?php

namespace App\Http\Controllers;

use App\Models\HandoutAttempt;
use App\Models\QuizAttempt;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

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

    // ---------- EXPORT ----------
    public function exportPretestExcel(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        $pretests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', fn ($q) => 
                $q->where('folder_type_id', 1) // Pretest
            )
            ->whereHas('quiz.folder.project', fn ($q) =>
                $q->where('user_id', Auth::id())
            )
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {

                    $q->whereHas('user', function ($u) use ($search) {
                        $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('grade_level', 'like', "%$search%")
                        ->orWhere('section', 'like', "%$search%");
                    })

                    ->orWhereHas('quiz.folder.project', function ($p) use ($search) {
                        $p->where('title', 'like', "%$search%");
                    })

                    ->orWhere('score', 'like', "%$search%")
                    ->orWhere('attempt_number', 'like', "%$search%");

                    if (preg_match('/^(\d+):(\d+)$/', $search, $m)) {
                        $q->orWhere('time_spent', ($m[1] * 60) + $m[2]);
                    }
                });
            })
            ->when($date, fn ($q) => $q->whereDate('created_at', $date))
            ->latest()
            ->get(); // 🔥 NOT paginate

        // ===============================
        // Excel Generation
        // ===============================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Row
        $headers = [
            'Student Name',
            'Username',
            'Grade',
            'Section',
            'Project Title',
            'Score',
            'Time Spent',
            'Attempt',
            'Date'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data Rows
        $row = 2;
        foreach ($pretests as $grade) {
            $sheet->fromArray([
                ($grade->user->first_name ?? '') . ' ' . ($grade->user->last_name ?? ''),
                $grade->user->username ?? '-',
                $grade->user->grade_level ?? '-',
                $grade->user->section ?? '-',
                $grade->quiz->folder->project->title ?? 'N/A',
                "{$grade->score} / {$grade->quiz->questions->count()}",
                gmdate('i:s', $grade->time_spent),
                $grade->attempt_number,
                // $grade->created_at->format('M d, Y h:i:s A'),
                $grade->created_at->addHours(8)->format('M d, Y h:i:s A'),
            ], null, "A{$row}");

            $row++;
        }

        $filename = 'pretest-grades-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }

    public function exportModuleExcel(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        // Fetch all filtered module attempts (without pagination)
        $modules = HandoutAttempt::with('user', 'handout.folder.project', 'handout')
            ->whereHas('handout.folder.project', fn($q) => $q->where('user_id', Auth::id()))
            ->when($search, function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->whereHas('user', fn($u) => $u->where('first_name', 'like', "%$search%")
                        ->orWhere('last_name', 'like', "%$search%")
                        ->orWhere('username', 'like', "%$search%")
                        ->orWhere('grade_level', 'like', "%$search%")
                        ->orWhere('section', 'like', "%$search%")
                    )
                    ->orWhereHas('handout.folder.project', fn($p) => $p->where('title', 'like', "%$search%"))
                    ->orWhere('attempt_number', 'like', "%$search%")
                    ->orWhere('time_spent', 'like', "%$search%");
                });
            })
            ->when($date, fn($q) => $q->whereDate('created_at', $date))
            ->latest()
            ->get(); // 🔥 all filtered records

        // ===============================
        // Excel Generation
        // ===============================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Row
        $headers = [
            'Student Name',
            'Username',
            'Grade',
            'Section',
            'Project Title',
            'Level',
            'Score',
            'Time Spent',
            'Attempt',
            'Date'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Map level_id to string
        $levelMap = [
            1 => 'Easy',
            2 => 'Average',
            3 => 'Hard',
        ];

        // Data Rows
        $row = 2;
        foreach ($modules as $m) {
            $levelName = $levelMap[$m->handout->level_id] ?? 'N/A';

            $sheet->fromArray([
                ($m->user->first_name ?? '') . ' ' . ($m->user->last_name ?? ''),
                $m->user->username ?? '-',
                $m->user->grade_level ?? '-',
                $m->user->section ?? '-',
                $m->handout->folder->project->title ?? 'N/A',
                $levelName, // now as text
                gmdate('i:s', $m->time_spent),
                $m->attempt_number,
                // $m->created_at->format('M d, Y h:i:s A'),
                $grade->created_at->addHours(8)->format('M d, Y h:i:s A'),
            ], null, "A{$row}");

            $row++;
        }

        $filename = 'module-grades-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            ob_clean();
            flush();
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }

    public function exportPosttestExcel(Request $request)
    {
        $search = $request->input('search');
        $date   = $request->input('date');

        // Fetch all filtered posttests (no pagination)
        $posttests = QuizAttempt::with('user', 'quiz.folder.project', 'quiz.questions')
            ->whereHas('quiz.folder', fn($q) => $q->where('folder_type_id', 3)) // Post-test
            ->whereHas('quiz.folder.project', fn($q) => $q->where('user_id', Auth::id()))
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
            ->when($date, fn($q) => $q->whereDate('created_at', $date))
            ->latest()
            ->get(); // get all filtered records

        // ===============================
        // Excel Generation
        // ===============================
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Header Row
        $headers = [
            'Student Name',
            'Username',
            'Grade',
            'Section',
            'Project Title',
            'Score',
            'Time Spent',
            'Attempt',
            'Date'
        ];
        $sheet->fromArray($headers, null, 'A1');

        // Data Rows
        $row = 2;
        foreach ($posttests as $grade) {
            $sheet->fromArray([
                ($grade->user->first_name ?? '') . ' ' . ($grade->user->last_name ?? ''),
                $grade->user->username ?? '-',
                $grade->user->grade_level ?? '-',
                $grade->user->section ?? '-',
                $grade->quiz->folder->project->title ?? 'N/A',
                "{$grade->score} / {$grade->quiz->questions->count()}",
                gmdate('i:s', $grade->time_spent),
                $grade->attempt_number,
                // $grade->created_at->format('M d, Y h:i:s A'),
                $grade->created_at->addHours(8)->format('M d, Y h:i:s A'),
            ], null, "A{$row}");

            $row++;
        }

        $filename = 'posttest-grades-' . now()->format('Ymd_His') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            ob_clean();
            flush();
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename);
    }
}