<?php

namespace App\Livewire;

use App\Models\Project;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProjectEditForm extends Component
{
    use WithFileUploads;

    public $project;
    public $title;
    public $description;
    public $folders = [];

    public $all_folder_types = [
        ['id' => 1, 'name' => 'Pretest'],
        ['id' => 2, 'name' => 'Module'],
        ['id' => 3, 'name' => 'Post-test'],
    ];

    public function mount(Project $project)
    {
        $this->project     = $project;

        $this->title       = $project->title;
        $this->description = $project->description;

        /*
            Using pluck()->toArray() is cleaner than the foreach loop.
            Automatically set selected folders
        */
        $this->folders = $project->folders->pluck('folder_type_id')->toArray();
    }

    public function edit($id)
    {
        $project = $this->project->findOrFail($id);

        if (Auth::user()->id != $project->user->id) {
            return redirect()->route('admin.projects');
        }

        $this->folders = $project->folders->pluck('folder_type_id')->toArray();
    }

    public function update()
    {
        $this->validate([
            'title'         => 'required|max:150', 
            'description'   => 'required|max:1000',
            'folders'       => 'required|array|between:1,3',
        ]);

         // Check the incoming data
        // dd([
        //     'title'       => $this->title,
        //     'description' => $this->description,
        //     'folders'     => $this->folders,
        // ]);

        $project = $this->project->findOrFail($this->project->id);

        $project->title           = $this->title;
        $project->description     = $this->description;
        $project->save();

        $project->folders()->delete();

        # Save the NEW FOLDERS to the folders table
        $folders = [];
        foreach($this->folders as $folder_type_id){
            $folders[] = ['folder_type_id' => $folder_type_id];
        }
        $project->folders()->createMany($folders);

        session()->flash('success', 'Project updated successfully!');
    
        return redirect()->route('admin.projects');
    }

    public function render()
    {
        return view('livewire.project-edit-form');
    }
}
