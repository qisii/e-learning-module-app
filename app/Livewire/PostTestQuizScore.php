<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Handout;
use App\Models\QuizAttempt;
use Livewire\Component;

class PostTestQuizScore extends Component
{
    public $quizAttempt;
    public $loading = true;
    public $scorePercentage;
    public $message;
    public $emoji;
    public $color;
    public $recommendedLevel;
    public $project_id;

    public function mount($quizAttemptId)
    {
        $this->quizAttempt = QuizAttempt::findOrFail($quizAttemptId);

        // Calculate score percentage
        $totalQuestions = $this->quizAttempt->quiz->questions->count();
        $this->scorePercentage = $totalQuestions > 0
            ? round(($this->quizAttempt->score / $totalQuestions) * 100)
            : 0;

        // Determine message, emoji, and color based on score
        if ($this->scorePercentage < 40) {
            $this->message = "Don't worry! You can do better next time!";
            $this->emoji = "💪";
            $this->color = "text-red-500";
        } elseif ($this->scorePercentage < 80) {
            $this->message = "Good job! You're getting there!";
            $this->emoji = "😊";
            $this->color = "text-yellow-500";
        } else {
            $this->message = "Amazing! You really nailed it!";
            $this->emoji = "🎉";
            $this->color = "text-green-600";
        }

        // Check session if loading has already been shown
        if (session()->has('quiz_score_loaded_' . $quizAttemptId)) {
            $this->loading = false;
        } else {
            $this->loading = true;
        }

        $this->recommendedLevel = $this->determineHandoutLevel();
        $this->project_id = $this->getProjectID();
    }

    public function render()
    {
        return view('livewire.post-test-quiz-score');
    }

    // Called from Alpine after 3 seconds
    public function finishLoading()
    {
        $this->loading = false;

        // Save in session to prevent future loading animation
        session(['quiz_score_loaded_' . $this->quizAttempt->id => true]);
    }

    // ---------- MODULE HANDOUT ----------
    /*
        Create a function that will determine the handout level based on the student's score.
            use the quiz_attempt to get the quiz_id,
            use quiz_id to get the folder_id,
            then use folder_id to get the project_id

            get the student's core,
            get all handouts of the project using the folder table that has folder_type_id is 2 then select all from handouts of that folder,

    */

    public function determineHandoutLevel()
    {
        $studentScore = $this->scorePercentage;

        $projectId = $this->getProjectID();

        if (!$projectId) {
            return null; // Project not found
        }

        // Get module folder (folder_type_id = 2)
        $moduleFolder = Folder::where('project_id', $projectId)
            ->where('folder_type_id', 2)
            ->first();

        if (!$moduleFolder) {
            return null;
        }

        $handouts = Handout::where('folder_id', $moduleFolder->id)
            ->leftJoin('handout_score', 'handouts.id', '=', 'handout_score.handout_id')
            ->select('handouts.*', 'handout_score.score as cutoff_score')
            ->orderBy('cutoff_score', 'asc')
            ->get();

        if ($handouts->isEmpty()) {
            return null;
        }

        // Find the highest cutoff <= student score
        $selected = $handouts
            ->filter(fn($h) => $studentScore >= $h->cutoff_score)
            ->sortByDesc('cutoff_score')
            ->first();

        return $selected ? $selected->level_id : $handouts->first()->level_id;
    }

    public function getProjectID()
    {
        // Check if quizAttempt exists
        if (!$this->quizAttempt) {
            return null;
        }

        // Get quiz
        $quiz = $this->quizAttempt->quiz;
        if (!$quiz) {
            return null;
        }

        // Get folder
        $folder = $quiz->folder;
        if (!$folder) {
            return null;
        }

        // Return project_id
        return $folder->project_id;
    }

}
