<?php

namespace App\Livewire;

use App\Models\Folder;
use App\Models\Quiz;
use Livewire\Component;
use Livewire\WithFileUploads;

class PretestQuizForm extends Component
{
    use WithFileUploads;

    public $quiz;
    public $folder;
    public $instructions;
    public $questions = [];

    public function mount(Folder $folder, ?Quiz $quiz = null)
    {
        $this->folder = $folder;
        $this->quiz = $quiz;

        // Restore from session if available
        // pulls those same values back out of the session.
        $this->instructions = session('instructions');
        $this->questions = session('questions', []);

        // If quiz exists and there is no session value for instructions, load from DB
        if ($quiz && $quiz->exists) {
            if (empty($this->instructions)) {
                $this->instructions = $quiz->instructions;
            }

            // Load questions only if not found in session
            if (empty($this->questions)) {
                $this->questions = $quiz->questions->map(function ($question, $index) {
                    return [
                        'id' => $question->id,
                        'label' => 'Question ' . ($index + 1),
                        'text' => $question->question_text,
                        'choices' => $question->choices->map(fn ($choice) => [
                            'id' => $choice->id,
                            'text' => $choice->choice_text,
                        ])->toArray(),
                        'correct_choice' => $question->choices->pluck('is_correct')->search(true),
                    ];
                })->toArray();
            }
        }

        // Default if new quiz and still empty
        if (empty($this->questions)) {
            $this->questions = [[
                'label' => 'Question 1',
                'text' => '',
                'choices' => [],
                'correct_choice' => null,
            ]];
        }

        // If no instructions at all, initialize empty (to avoid null in textarea)
        if (empty($this->instructions)) {
            $this->instructions = '';
        }
    }

    // saves those updated values into the Laravel session.
    public function updated($propertyName)
    {
        session([
            'instructions' => $this->instructions,
            'questions' => $this->questions,
        ]);
    }

    public function addQuestion()
    {
        $this->questions[] = [
            'label' => 'Question ' . (count($this->questions) + 1),
            'text' => '',
            'choices' => [],
            'correct_choice' => null,
        ];
        session(['questions' => $this->questions]);
        
    }

    public function addChoice($qIndex)
    {
        if (count($this->questions[$qIndex]['choices']) < 4) {
            $this->questions[$qIndex]['choices'][] = ['text' => ''];
            session(['questions' => $this->questions]);
        }
    }
        
    public function store()
    {
        $this->validate(
            [
                'instructions' => 'required|string|max:1000',
                'questions' => 'required|array|min:1',
                'questions.*.text' => 'required|string|max:1000',
                'questions.*.choices' => 'nullable|array|min:1',
            ],
            [
                'instructions.required' => 'The instructions field is required.',
                'questions.required' => 'You must add at least one question.',
                'questions.*.text.required' => 'The question field is required.',
                'questions.*.text.max' => 'The question must not exceed 1000 characters.',
                'questions.*.choices.min' => 'Each question must have at least one choice.',
            ]
        );

        // ✅ Create or Update the quiz
        if ($this->quiz && $this->quiz->exists) {
            // Update existing quiz
            $this->quiz->update([
                'instructions' => $this->instructions,
            ]);

            // Remove old questions (to reinsert cleanly)
            $this->quiz->questions()->each(function ($question) {
                $question->choices()->delete();
                $question->delete();
            });

            $quiz = $this->quiz;
        } else {
            // Create new quiz for the folder
            $quiz = $this->folder->quizzes()->create([
                'instructions' => $this->instructions,
            ]);
        }

        // ✅ Store questions and choices
        foreach ($this->questions as $qIndex => $questionData) {
            $question = $quiz->questions()->create([
                'question_text' => $questionData['text'],
            ]);

            $choices = [];
            foreach ($questionData['choices'] as $cIndex => $choiceData) {
                $choices[] = [
                    'choice_label_id' => $cIndex + 1, // A=1, B=2, C=3, D=4
                    'choice_text' => $choiceData['text'] ?? '',
                    'is_correct' => isset($questionData['correct_choice']) && $questionData['correct_choice'] == $cIndex,
                ];
            }

            $question->choices()->createMany($choices);
        }

        // ✅ Clear stored session data
        session()->forget(['instructions', 'questions']);
        
        // After quiz and questions saved
        $this->quiz->folder->updated_at = now();
        $this->quiz->folder->saveQuietly();

        // $this->dispatch('flashMessage', type: 'success', message: 'Quiz updated successfully!');
        $message = $this->quiz ? 'Quiz updated successfully!' : 'Quiz created successfully!';
        $this->dispatch('flashMessage', type: 'success', message: $message);

    }

    // public function deleteQuestion($qIndex)
    // {
    //     $question = $this->questions[$qIndex] ?? null;

    //     // Delete from DB if it has an ID (means it's saved)
    //     if ($question && isset($question['id'])) {
    //         $this->quiz->questions()->where('id', $question['id'])->delete();
    //     }

    //     unset($this->questions[$qIndex]);
    //     $this->questions = array_values($this->questions);
    //     session(['questions' => $this->questions]);
    // }
    public function deleteQuestion($qIndex)
    {
        $question = $this->questions[$qIndex] ?? null;

        // Delete from DB if it has an ID (means it's saved)
        if ($question && isset($question['id']) && $this->quiz && $this->quiz->exists) {
            $this->quiz->questions()->where('id', $question['id'])->delete();
        }

        // Remove from array
        unset($this->questions[$qIndex]);
        $this->questions = array_values($this->questions); // Re-index numerically

        // Re-label questions after deletion
        foreach ($this->questions as $index => &$question) {
            $question['label'] = 'Question ' . ($index + 1);
        }

        // Update session
        session(['questions' => $this->questions]);
    }

    public function deleteChoice($qIndex, $cIndex)
    {
        $choice = $this->questions[$qIndex]['choices'][$cIndex] ?? null;

        if ($choice && isset($choice['id'])) {
            \App\Models\Choice::where('id', $choice['id'])->delete();
        }

        unset($this->questions[$qIndex]['choices'][$cIndex]);
        $this->questions[$qIndex]['choices'] = array_values($this->questions[$qIndex]['choices']);
        session(['questions' => $this->questions]);
    }

    public function render()
    {
        return view('livewire.pretest-quiz-form');
    }
}

/*
array:2 [▼ // app\Livewire\PretestQuizForm.php:65
  "instructions" => "This is intructions"
  "questions" => array:2 [▼
    0 => array:4 [▼
      "label" => "Question 1"
      "text" => "The genetic makeup of an organism, represented by its alleles, is called its:"
      "choices" => array:4 [▼
        0 => array:1 [▶]
        1 => array:1 [▼
          "text" => "Genotype"
        ]
        2 => array:1 [▶]
        3 => array:1 [▶]
      ]
      "correct_choice" => "1"
    ]
    1 => array:4 [▼
      "label" => "Question 2"
      "text" => "The observable characteristics of an organism, such as eye color or height, refer to its:"
      "choices" => array:4 [▼
        0 => array:1 [▶]
        1 => array:1 [▼
          "text" => "Phenotype"
        ]
        2 => array:1 [▶]
        3 => array:1 [▶]
      ]
      "correct_choice" => "1"
    ]
  ]
]
*/