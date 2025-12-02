<?php

namespace App\Http\Controllers;

use App\Models\Folder;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectsController extends Controller
{
    private $project;
    private $folder;

    public function __construct(Project $project, Folder $folder)
    {
        $this->project  = $project;
        $this->folder   = $folder;
    }

    public function index(){
        // Safely clear all custom session data EXCEPT AUTH USER
        $this->removeSession();

        $all_projects = $this->project->where('user_id', Auth::user()->id)->latest()->paginate(4);

        return view('admin.projects.index')
                ->with('all_projects', $all_projects);
    }

    // public function removeSession(){
    //     // Get all session keys
    //     $allKeys = array_keys(session()->all());

    //     // Exclude authentication-related keys
    //     $protectedKeys = [
    //         '_token',
    //         '_previous',
    //         '_flash',
    //     ];

    //     // Laravel often stores the login session using something like "login_web_xxx"
    //     foreach ($allKeys as $key) {
    //         if (str_starts_with($key, 'login_web')) {
    //             $protectedKeys[] = $key;
    //         }
    //     }

    //     // Forget everything except the protected ones
    //     $keysToForget = array_diff($allKeys, $protectedKeys);

    //     session()->forget($keysToForget);
    // }

    protected function removeSession()
    {
        // All current keys
        $allKeys = array_keys(session()->all());

        // Base protected keys (Laravel internals + csrf)
        $protectedKeys = [
            '_token',
            '_previous',
            '_flash',
            '_flash.old',
            '_flash.new',
            '_csrf_token', // if present in some apps
        ];

        // Protect auth keys like login_web_xxx or login_??? (covers common guards)
        foreach ($allKeys as $key) {
            if (str_starts_with($key, 'login_')) {
                $protectedKeys[] = $key;
            }
        }

        // Also protect any currently-flashed keys (so flash messages survive)
        $flashNew = session()->get('_flash.new', []);
        $flashOld = session()->get('_flash.old', []);

        $flashKeys = array_unique(array_merge(
            is_array($flashNew) ? $flashNew : [],
            is_array($flashOld) ? $flashOld : []
        ));

        foreach ($flashKeys as $fKey) {
            $protectedKeys[] = $fKey;
        }

        // Deduplicate just in case
        $protectedKeys = array_unique($protectedKeys);

        // Keys to forget = all - protected
        $keysToForget = array_diff($allKeys, $protectedKeys);

        session()->forget($keysToForget);
    }


    public function create(){
        return view('admin.projects.create');
    }

    public function edit($id){
        $project = $this->project->findOrFail($id);

        return view('admin.projects.edit')
                ->with('project', $project);
    }

    public function delete($id)
    {
        $project = $this->project->findOrFail($id);
        $name = $project->title; // raw, not escaped here
        $project->delete();

        session()->flash('error', [
            'name' => $name,
            'message' => "$name was deleted!",
        ]);

        return redirect()->route('admin.projects');
    }
        
}
