<?php

namespace App\Livewire;

use App\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class AnalysisData extends Component
{
    public $genderFilter = '';
    public $gradeFilter = '';
    public $sectionFilter = '';

    protected $listeners = ['refreshCharts' => '$refresh'];

    public function updated($property)
    {
        // Whenever a filter updates, dispatch new data to JS
        $this->dispatchChartsUpdate();
    }

    public function mount()
    {
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

    public function getDateTimeProperty()
    {
        $now = Carbon::now('Asia/Manila');
        $isDay = $now->hour >= 6 && $now->hour < 18;

        return [
            'icon' => $isDay ? '☀️' : '🌙',
            'text' => $now->format('l, F j Y • h:i A'),
        ];
    }

    public function render()
    {
        // $this->dispatchChartsUpdate();
        return view('livewire.analysis-data');
    }
}
