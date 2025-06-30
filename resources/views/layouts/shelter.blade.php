@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/shelter/shelter.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('navbar')
{{-- Hide navbar on shelter pages --}}
@overwrite

@section('content')
    <div class="shelter-layout">
        @include('components.shelter-sidebar')
        
        <main class="shelter-content">
            <header class="bg-white shadow">
                <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8 flex justify-between items-center">
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                        @yield('header')
                    </h2>
                    
                    @php
                        $shelter = Auth::user()->shelter;
                        $verification = null;
                        if ($shelter) {
                            $verification = App\Models\Shelter\ShelterVerification::where('shelter_id', $shelter->shelter_id)
                                ->latest()
                                ->first();
                        }
                    @endphp

                    <!-- Work in Progress -->
                    <!-- <div class="flex items-center space-x-4">
                        @if($verification)
                            <div class="px-4 py-2 rounded-full text-sm font-medium
                                {{ $verification->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 
                                   ($verification->status === 'approved' ? 'bg-green-100 text-green-800' : 
                                    'bg-red-100 text-red-800') }}">
                                Verification Status: {{ ucfirst($verification->status) }}
                            </div>
                        @else
                            <a href="{{ route('shelter.verification.form') }}" 
                               class="px-4 py-2 rounded-full text-sm font-medium bg-blue-100 text-blue-800 hover:bg-blue-200">
                                Get Verified
                            </a>
                        @endif
                    </div> -->
                    <!-- Work in Progress -->
                    
                </div>
            </header>
            @yield('shelter-content')
        </main>
    </div>
@endsection