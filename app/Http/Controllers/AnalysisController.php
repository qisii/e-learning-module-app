<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class AnalysisController extends Controller
{
    private $project;
    
    public function __construct(Project $project)
    {
        $this->project = $project;
    }

    public function index(){
        return view('admin.analysis.index');
    }
}
