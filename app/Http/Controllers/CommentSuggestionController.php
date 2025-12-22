<?php

namespace App\Http\Controllers;

use App\Models\CommentSuggestion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentSuggestionController extends Controller
{
    private $commentSuggestion;
    private $user;

    public function __construct(CommentSuggestion $commentSuggestion, User $user)
    {
        $this->commentSuggestion = $commentSuggestion;
        $this->user = $user;
    }

    public function index(){
        return view('users.comments-suggestions.index');
    }

    public function store(Request $request){
        // 1. Validate the request
        $request->validate([
            'body' => 'required|max:300'
        ]);

        // 2. Save the form data to comments table
        $this->commentSuggestion->user_id     = Auth::user()->id;
        $this->commentSuggestion->body        = $request->body;
        $this->commentSuggestion->save();

        return redirect()->route('comments.suggestions.message');
    }

    public function message(){
        return view('users.comments-suggestions.message');
    }

}
