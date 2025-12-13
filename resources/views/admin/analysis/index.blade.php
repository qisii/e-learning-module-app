@extends('layouts.app')

@section('title', 'Analysis')
@section('header', 'Analysis')
@section('analysis-active', 'bg-[#0F2250] text-blue-300')

@section('content')
    <div class="w-full p-4 lg:p-10 md:p-10 overflow-auto no-scrollbar font-secondary">

        <livewire:analysis-data />
    </div>
@endsection

@push('scripts')
    @vite(['resources/js/charting.js'])
@endpush
