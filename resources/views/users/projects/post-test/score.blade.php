@extends('layouts.app')

@section('title', $quiz_attempt->quiz->folder->project->title)

@section('header', $quiz_attempt->quiz->folder->project->title)

@section('projects-active', 'bg-[#0F2250] text-blue-300')

@section('content')
    <div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar">
        {{-- display loading screen --}}
        <livewire:post-test-quiz-score :quiz-attempt-id="$quiz_attempt->id">
    </div>
@endsection