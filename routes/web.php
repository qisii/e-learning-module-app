<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\CommentsSuggestionsController;
use App\Http\Controllers\CommentSuggestionController;
use App\Http\Controllers\GradeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectsController;
use App\Http\Controllers\QuizzesController;
use App\Http\Controllers\SocialiteController;
use App\Livewire\ProfileForm;
use App\Livewire\QuizScore;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });

// Login with Google

Route::get('/auth/google', [SocialiteController::class, 'googleLogin'])->name('auth.google');
Route::get('/auth/google-callback', [SocialiteController::class, 'googleAuthentication'])->name('auth.google-callback');

Route::group(['middleware' => 'auth'], function(){
     // add {id}
    // Route::get('/edit', [ProfileController::class, 'edit'])->name('edit');
    // Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    # LIVEWIRE
    // Route::get('/profile/show', ProfileForm::class)->name('profile.show');

    # ADMIN
    Route::group(['prefix' => 'admin', 'as' => 'admin.', 'middleware' => 'admin'], function(){
        Route::get('/profile/show', [ProfileController::class, 'showAdmin'])->name('profile.show');
        // Route::patch('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

        # PROJECTS
        Route::get('/projects', [ProjectsController::class, 'index'])->name('projects');
        Route::get('/project/create', [ProjectsController::class, 'create'])->name('projects.create');
        Route::get('/project/{id}/edit', [ProjectsController::class, 'edit'])->name('projects.edit');
        Route::delete('/project/{id}/delete', [ProjectsController::class, 'delete'])->name('projects.delete');

        # QUIZ
        Route::get('/{folder_id}/pretest/quiz', [QuizzesController::class, 'showPretest'])->name('quiz.pretest.show');
        Route::get('/{folder_id}/post-test/quiz', [QuizzesController::class, 'showPostTest'])->name('quiz.posttest.show');

        # MODULE
        Route::get('/{folder_id}/module/', [QuizzesController::class, 'showModule'])->name('module.show');
        Route::get('/{folder_id}/module/preview', [QuizzesController::class, 'previewModule'])->name('module.preview');
        Route::get('/refresh', [QuizzesController::class, 'refresh'])->name('refresh');

        # QUESTION
        Route::delete('/quiz/{question_id}/delete', [QuizzesController::class, 'deleteQuestion'])->name('questions.delete');

        # GRADES
        Route::get('/grades/pretests', [GradeController::class, 'indexPretestAdmin'])->name('grades.pretest');
        Route::get('/grades/post-tests', [GradeController::class, 'indexPostTestAdmin'])->name('grades.posttest');
        Route::get('/grades/modules', [GradeController::class, 'indexModuleAdmin'])->name('grades.module');

        # SEARCH
        Route::get('/grades/pretests/search', [GradeController::class, 'searchPretestAdmin'])->name('grades.pretest.search');
        Route::get('/grades/post-tests/search', [GradeController::class, 'searchPostTestAdmin'])->name('grades.posttest.search');
        Route::get('/grades/modules/search', [GradeController::class, 'searchModuleAdmin'])->name('grades.module.search');

        # ANALYSIS
        Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');

        # COMMENTS & SUGGESTIONS
        Route::get('/comments-suggestions', [CommentsSuggestionsController::class, 'index'])->name('comments.suggestions.index');
        Route::get('/comments-suggestions/search', [CommentsSuggestionsController::class, 'search'])->name('comments.suggestions.search');
    });

    # USER
    Route::group(['middleware' => 'user'], function(){
        # PROFILE
        Route::get('/profile/show', [ProfileController::class, 'show'])->name('profile.show');
        # PROJECT
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        # PRETEST
        Route::get('/project/{project_id}/pretest/welcome', [ProjectController::class, 'welcomePretest'])->name('projects.welcome.pretest');
        Route::get('/project/{project_id}/pretest/quiz', [ProjectController::class, 'showPretest'])->name('projects.show.pretest');
        # MODULE HANDOUT
        Route::get('/project/{project_id}/module/{level_id}/show', [ProjectController::class, 'showModule'])->name('projects.module.show');
        Route::post('/project/{handout_id}/module/store', [ProjectController::class, 'storeHandoutAttempt'])->name('projects.module.attempt.store');
        # POST TEST
        Route::get('/project/{project_id}/post-test/welcome', [ProjectController::class, 'welcomePostTest'])->name('projects.welcome.posttest');
        Route::get('/project/{project_id}/post-test/quiz', [ProjectController::class, 'showPostTest'])->name('projects.show.posttest');

        # SCORE
        Route::get('/pretest/quiz/score/{quizAttemptId}', [ProjectController::class, 'openPreScore'])->name('pre.quiz.score');
        Route::get('/post_test/quiz/score/{quizAttemptId}', [ProjectController::class, 'openPostScore'])->name('post.quiz.score');

        # GRADE
        Route::get('/grades/pretests', [GradeController::class, 'indexPretest'])->name('grades.index');
        Route::get('/grades/post-tests', [GradeController::class, 'indexPostTest'])->name('grades.posttest');

        # SEARCH
        Route::get('/grades/pretests/search', [GradeController::class, 'searchPretest'])->name('grades.pretest.search');
        Route::get('/grades/post-tests/search', [GradeController::class, 'searchPostTest'])->name('grades.posttest.search');

        # COMMENTS & SUGGESTIONS
        Route::get('/comments-suggestions', [CommentSuggestionController::class, 'index'])->name('comments.suggestions.index');
        Route::post('/comments-suggestions/store', [CommentSuggestionController::class, 'store'])->name('comments.suggestions.store');
        Route::get('/comments-suggestions/message', [CommentSuggestionController::class, 'message'])->name('comments.suggestions.message');
    });
});

require __DIR__.'/auth.php';
