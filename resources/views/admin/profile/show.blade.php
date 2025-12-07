@extends('layouts.app')

@section('title', 'Admin Profile')

@section('header', 'Admin Profile')

@section('account-active', 'bg-[#0F2250] text-blue-300')

@section('content')
<div class="h-auto">
    <div class="w-full h-[400px] bg-cover bg-bottom relative"
        style="background-image: url('{{ asset('assets/images/profile-header-simple1.png') }}');">
        <div class="w-full px-4 lg:px-10 md:px-10 absolute translate-y-[10%]">
            <div class="bg-white w-full mx-auto shadow-lg rounded-lg p-0 pt-6 mb-5 lg:p-6 md:p-6 mb-6 lg:mb-6">

                <livewire:admin-profile-form :user="$user" />

            </div>
        </div>
    </div>
</div>    
@endsection


