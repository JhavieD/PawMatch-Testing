@extends('layouts.adopter')

@section('title', 'Report a Stray Animal - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/adopter/report-stray.css') }}">
@endpush

@section('adopter-content')

<div class="main-container">
    {{-- Success Message --}}
    @if(session('success'))
        <div id="success-toast" class="alert-success">
            <div class="alert-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <div>
                <strong>Report Submitted Successfully!</strong><br>
                {{ session('success') }}
            </div>
        </div>
    @endif

    {{-- Validation Errors --}}
    @if($errors->any())
        <div class="alert alert-danger">
            <div class="alert-icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <div class="alert-content">
                <h4>Please fix the following errors:</h4>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <div class="report-form">
        <div class="form-header">
            <div class="header-content">
                <h1>Report a Stray Animal</h1>
                <p>Help us locate and care for stray animals in your area. Your report could save a life.</p>
                <span></span>
            </div>
        </div>
        <form method="POST" action="{{ route('stray.report.submit') }}" enctype="multipart/form-data" id="strayReportForm">
            @csrf
            <div class="form-section">
                <div class="form-group">
                    <label for="animalType">
                        Animal Type
                    </label>
                    <select id="animalType" name="animalType" required>
                        <option value="">Select animal type</option>
                        <option value="dog">Dog</option>
                        <option value="cat">Cat</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">
                        Description
                    </label>
                    <textarea id="description" name="description" placeholder="Describe the animal's appearance, behavior, and condition." required></textarea>
                    <div class="char-counter">
                        <span id="descriptionCount">0</span>/500 characters
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        Photos
                    </label>
                    <div class="image-upload-container">
                        <div class="image-upload" id="imageUploadArea">
                            <input type="file" id="fileInput" name="photos[]" multiple accept="image/*" style="display:none;">
                            <div class="upload-content">
                                <h3>Upload Photos</h3>
                                <p>Drag and drop images here or click to browse</p>
                                <span class="upload-hint">Supports JPG, PNG, GIF (Max 5MB each)</span>
                            </div>
                        </div>
                        <div class="image-preview" id="imagePreview">
                            <!-- Preview images will be added here -->
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Address</label>
                    <div class="location-group">
                        <input type="text" name="street" placeholder="Street Address" required>
                        <input type="text" name="city" placeholder="City" required>
                        <input type="text" name="zip" placeholder="ZIP Code" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="landmarks">
                        Nearby Landmarks
                    </label>
                    <textarea id="landmarks" name="landmarks" placeholder="Describe any nearby landmarks, businesses, or notable locations."></textarea>
                </div>
            </div>
            <div class="form-actions">
                <button type="submit" class="btn btn-success">
                    Submit Report
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const imageUploadArea = document.getElementById('imageUploadArea');
    const imagePreview = document.getElementById('imagePreview');
    const descriptionTextarea = document.getElementById('description');
    const descriptionCount = document.getElementById('descriptionCount');
    const toast = document.getElementById('success-toast');

    // Character counter for description
    descriptionTextarea.addEventListener('input', function() {
        const count = this.value.length;
        descriptionCount.textContent = count;
        if (count > 450) {
            descriptionCount.style.color = '#ef4444';
        } else if (count > 400) {
            descriptionCount.style.color = '#f59e0b';
        } else {
            descriptionCount.style.color = '#6b7280';
        }
    });

    // Image upload functionality
    imageUploadArea.addEventListener('click', () => fileInput.click());
    imageUploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        imageUploadArea.classList.add('dragover');
    });
    imageUploadArea.addEventListener('dragleave', () => {
        imageUploadArea.classList.remove('dragover');
    });
    imageUploadArea.addEventListener('drop', (e) => {
        e.preventDefault();
        imageUploadArea.classList.remove('dragover');
        const files = e.dataTransfer.files;
        handleFiles(files);
    });
    fileInput.addEventListener('change', function(event) {
        handleFiles(event.target.files);
    });
    function handleFiles(files) {
        imagePreview.innerHTML = '';
        Array.from(files).forEach((file, index) => {
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = document.createElement('div');
                    previewContainer.className = 'preview-container';
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'preview-image';
                    const removeBtn = document.createElement('button');
                    removeBtn.className = 'remove-image';
                    removeBtn.innerHTML = '✕';
                    removeBtn.onclick = function() {
                        previewContainer.remove();
                    };
                    previewContainer.appendChild(img);
                    previewContainer.appendChild(removeBtn);
                    imagePreview.appendChild(previewContainer);
                };
                reader.readAsDataURL(file);
            }
        });
    }

    if (toast) {
        setTimeout(() => {
            toast.classList.add('show');
        }, 100); // slight delay for animation
        setTimeout(() => {
            toast.classList.remove('show');
            window.location.href = "{{ route('adopter.my-reports') }}";
        }, 2100); // 2 seconds visible, then redirect
    }
});
</script>
@endpush
@endsection