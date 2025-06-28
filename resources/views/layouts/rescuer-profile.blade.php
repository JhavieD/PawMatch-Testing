@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/rescuer/profile.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('navbar')
{{-- Hide navbar on rescuer pages --}}
@overwrite

@section('content')
<div class="rescuer-dashboard-container">
    @include('components.rescuer-sidebar')

    <div class="main-content-wrapper">
        @yield('rescuer-content')
    </div>
</div>
@endsection