@extends('layouts.adopter')

@section('title', 'Report a Stray Animal - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/report-stray.css') }}">
@endpush

@section('adopter-content')
<nav>
    <div class="nav-container">
        <a href="{{ url('/') }}" class="logo">PawMatch</a>
    </div>
</nav>

<div class="main-container">
    {{-- Success Message --}}
    @if(session('success'))
        <div class="alert alert-success" style="background:#d1fae5;color:#065f46;padding:1rem;border-radius:8px;margin-bottom:1rem;">
            {{ session('success') }}
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger" style="background:#fee2e2;color:#991b1b;padding:1rem;border-radius:8px;margin-bottom:1rem;">
            <ul style="margin:0;padding-left:1.2em;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="report-form" method="POST" action="{{ route('stray.report.submit') }}" enctype="multipart/form-data">
        @csrf
        <h1>Report a Stray Animal</h1>
        
        <div class="form-group">
            <label for="animalType">Animal Type</label>
            <select id="animalType" name="animalType" required>
                <option value="">Select animal type</option>
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="other">Other</option>
            </select>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea id="description" name="description" placeholder="Describe the animal's appearance, behavior, and condition..." required></textarea>
        </div>

        <div class="form-group">
            <label>Photos</label>
            <div class="image-upload" onclick="document.getElementById('fileInput').click()">
                <input type="file" id="fileInput" name="photos[]" multiple accept="image/*" style="display:none;">
                <p>Click to upload photos</p>
                <p class="help-text">You can upload multiple photos</p>
            </div>
            <div class="image-preview" id="imagePreview">
                <!-- Preview images will be added here -->
            </div>
        </div>

        <div class="form-group">
            <label>Location</label>
            <div class="location-group">
                <input type="text" name="street" placeholder="Street Address" required style="font-family: 'Inter', sans-serif;">
                <input type="text" name="city" placeholder="City" required style="font-family: 'Inter', sans-serif;">
            </div>
            <div class="location-group" style="margin-top: 1rem;">
                <input type="text" name="zip" placeholder="ZIP Code" required style="font-family: 'Inter', sans-serif;">
            </div>
        </div>  

        <div class="form-group">
            <label for="landmarks">Nearby Landmarks</label>
            <textarea id="landmarks" name="landmarks" placeholder="Describe any nearby landmarks or notable locations..."></textarea>
        </div>

        <div class="form-group">
            <label for="contactName">Your Contact Information</label>
            <input type="text" id="contactName" name="contactName" placeholder="Your Name" required style="font-family: 'Inter', sans-serif;">
        </div>

        <div class="form-group">
            <input type="tel" id="contactPhone" name="contactPhone" placeholder="Phone Number" required style="font-family: 'Inter', sans-serif;">
        </div>

        <div class="form-group">
            <input type="email" id="contactEmail" name="contactEmail" placeholder="Email Address" required style="font-family: 'Inter', sans-serif;">
        </div>

        <button type="submit" class="btn" style="font-family: 'Inter', sans-serif;">Submit Report</button>
    </form>
</div>

@push('scripts')
<script>
document.getElementById('fileInput').addEventListener('change', function(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    Array.from(event.target.files).forEach(file => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.style.maxWidth = '100px';
            img.style.margin = '5px';
            preview.appendChild(img);
        };
        reader.readAsDataURL(file);
    });
});
</script>
@endpush
@endsection