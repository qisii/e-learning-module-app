<?php

namespace App\Http\Controllers;

use App\Models\CommentSuggestion;
use App\Models\User;
use Illuminate\Http\Request;

class CommentsSuggestionsController extends Controller
{
    private $commentSuggestion;
    private $user;

    public function __construct(CommentSuggestion $commentSuggestion, User $user)
    {
        $this->commentSuggestion = $commentSuggestion;
        $this->user = $user;
    }

    public function index()
    {
        // get all the comments_suggestions data
        // display the full name, username, grade, section, created_at(date)
        $comments = $this->commentSuggestion
            ->with('user')
            ->latest()
            ->paginate(10);

        return view('admin.comments-suggestions.index', compact('comments'));
    }

    public function search(Request $request)
    {
        $search = $request->search;
        $date   = $request->date;

        $comments = CommentSuggestion::with('user')
            ->whereHas('user', function ($query) use ($search) {
                $query->where('first_name', 'like', "%{$search}%")
                    ->orWhere('last_name', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%");
            })
            ->when($date, function ($query) use ($date) {
                $query->whereDate('created_at', $date);
            })
            ->latest()
            ->paginate(10);

        return view('admin.comments-suggestions.index', compact('comments'));
    }

}
