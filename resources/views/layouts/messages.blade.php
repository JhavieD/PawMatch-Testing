@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/shelter/messages.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('navbar')
{{-- Hide navbar on shelter pages --}}
@overwrite

@section('content')
<div class="shelter-layout">
    @include('components.shelter-sidebar')

    <main class="shelter-content">
        @yield('shelter-content')
    </main>
</div>
@endsection