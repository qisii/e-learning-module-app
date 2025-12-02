<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class StudentPostTestQuizForm extends Component
{
    use WithFileUploads;

    public $project;
    public $quiz;
    public $answers = []; // question_id => choice_label_id
    public $seconds = 0; // Time spent in seconds
    public $timerRunning = true; // Control timer polling
    public $bgImage;

    public function mount(Project $project, Quiz $quiz, QuizAttempt $quizAttempt)
    {
        $this->project = $project;
        $this->quiz = $quiz;

        $this->bgImage = $this->getBackgroundImage();

        // Load previous answers from session
        $this->answers = session('quiz_answers', []);
        // Load previous timer from session
        $this->seconds = session('quiz_timer', 0);
    }

    protected function getBackgroundImage()
    {
        $bgImages = [
            'quiz-bg.png',
            'quiz-bg-1.png',
            'quiz-bg-2.png',
            'quiz-bg-3.png',
        ];

        // Use a stable key — quiz ID keeps it consistent for each quiz
        $index = $this->quiz->id % count($bgImages);

        return asset('assets/images/' . $bgImages[$index]);
    }

    // Save choice in session
    public function selectChoice($questionId, $choiceId)
    {
        $this->answers[$questionId] = $choiceId;
        session(['quiz_answers' => $this->answers]);
    }

    // Increment timer every second
    public function tick()
    {
        if ($this->timerRunning) {
            $this->seconds++;
            session(['quiz_timer' => $this->seconds]); // Persist timer in session
        }
    }

    // Validate answers, calculate score
    public function checkAnswers()
    {
        // 1. Validate
        $rules = [];
        $messages = [];
        foreach ($this->quiz->questions as $question) {
            $rules['answers.' . $question->id] = 'required';
            $messages['answers.' . $question->id . '.required'] = 'The answer field is required';
        }

        $this->validate($rules, $messages);

        // Stop timer AFTER successful validation
        $this->timerRunning = false;

        // 2. Calculate score
        $score = 0;
        foreach ($this->quiz->questions as $question) {
            $userChoiceId = $this->answers[$question->id] ?? null;
            $correctChoice = $question->choices()->where('is_correct', true)->first();
            if ($correctChoice && $userChoiceId == $correctChoice->choice_label_id) {
                $score++;
            }
        }

        // 3. Store the quiz attempt
        $this->store($score);

        // Optional: Clear quiz session after storing
        session()->forget(['quiz_answers', 'quiz_timer']);
    }

    // Store quiz attempt
    protected function store($score)
    {
        $userId = Auth::id();

        $lastAttempt = QuizAttempt::where('quiz_id', $this->quiz->id)
            ->where('user_id', $userId)
            ->latest('attempt_number')
            ->first();

        $attemptNumber = $lastAttempt ? $lastAttempt->attempt_number + 1 : 1;

        $quizAttempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $userId,
            'attempt_number' => $attemptNumber,
            'score' => $score,
            'time_spent' => $this->seconds,
        ]);

        // Redirect to score page
        return redirect()->route('post.quiz.score', ['quizAttemptId' => $quizAttempt->id]);
    }

    public function render()
    {
        return view('livewire.student-post-test-quiz-form');
    }
}
