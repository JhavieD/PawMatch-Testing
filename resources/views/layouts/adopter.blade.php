@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/adopter/adopter.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('navbar')
{{-- Hide navbar on adopter pages --}}
@overwrite

@section('content')
    <div class="adopter-layout">
        @include('components.adopter-sidebar')
        
        <main class="adopter-content">
            @yield('adopter-content')
        </main>
    </div>
@endsection
