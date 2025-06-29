@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/rescuer/messages.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('navbar')
    {{-- Hide navbar on rescuer pages --}}
@overwrite

@section('content')
    <div class="rescuer-layout">
        @include('components.rescuer-sidebar')

        <main class="rescuer-content">
            @yield('rescuer-content')
        </main>
    </div>
@endsection
