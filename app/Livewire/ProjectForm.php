<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProjectForm extends Component
{
    use WithFileUploads;

    public $project;

    public $title;
    public $description;
    public $folders = [];


    public function mount(Project $project){
        $this->project = $project;
    }

    public function store()
    {
        $this->validate([
            'title'         => 'required|max:150', 
            'description'   => 'required|max:1000',
            'folders'       => 'required|array|between:1,3',
        ]);

        $this->project->title           = $this->title;
        $this->project->description     = $this->description;
        $this->project->user_id         = Auth::user()->id;
        $this->project->save();

        # Save the folders to the folders table
        $folders = [];
        foreach($this->folders as $folder_type_id){
            $folders[] = ['folder_type_id' => $folder_type_id];
        }
        $this->project->folders()->createMany($folders);

        session()->flash('success', 'Project created successfully!');
    
        return redirect()->route('admin.projects');
    }

    public function render()
    {
        return view('livewire.project-form');
    }
}
