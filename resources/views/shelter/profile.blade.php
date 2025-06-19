@extends ('layouts.shelter-profile')

@section('title', 'Profile')

@section('shelter-content')
<div class="container">
    <div class="header">
        <h1>Settings</h1>
        <p>Manage your account preferences and profile information</p>
    </div>

    <div class="settings-grid">
        <!-- Profile Settings -->
        <div class="settings-card">
            <div class="card-header">
                <h2>Profile Information</h2>
            </div>
            <div class="card-content">
                <div class="profile-upload">
                    <img src="{{ Auth::user()->profile_photo_url ?? asset('images/default-profile.png') }}" alt="Profile" class="profile-image" />
                    <div class="upload-buttons">
                        <button class="btn btn-outline">Upload New Photo</button>
                        <button class="btn btn-outline">Remove</button>
                    </div>
                </div>

                <form>
                    <label for="name" class="form-label" style="display: flex; align-items: center;">
                        @php
                            $verification = App\Models\ShelterVerification::where('shelter_id', Auth::user()->shelter->shelter_id)
                                ->latest()
                                ->first();
                        @endphp
                        @if($verification && $verification->status === 'approved')
                            Verified <img src="https://img.icons8.com/ios-filled/50/000000/verified-account.png" alt="Verified Icon" style="width: 24px; height: 24px; margin-left: 0.5rem;">
                        @endif
                    </label>
                    <div class="form-group">
                        <label for="name" class="form-label">Shelter Name</label>
                        <input type="text" id="name" class="form-input" value="{{ Auth::user()->shelter->name }}" />
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Last Name</label>
                        <input type="text" id="name" class="form-input" value="{{ Auth::user()->last_name }}" />
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">First Name</label>
                        <input type="text" id="name" class="form-input" value="{{ Auth::user()->first_name }}" />
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" class="form-input" value="{{ Auth::user()->email }}" />
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Contact Number</label>
                        <input type="tel" id="phone" class="form-input" value="{{ Auth::user()->shelter->contact_number }}" />
                    </div>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </form>
            </div>
        </div>

        <!-- Verification Settings -->
        <div class="settings-card">
            <div class="card-header">
                <h2>Account Verification</h2>
            </div>
            <div class="card-content">
                @if($verification)
                    <div class="verification-status {{ $verification->status }}">
                        <p class="status-text">Status: {{ ucfirst($verification->status) }}</p>
                        <p class="submission-date">Submitted: {{ \Carbon\Carbon::parse($verification->submitted_at)->format('M d, Y') }}</p>
                        @if($verification->reviewed_at)
                            <p class="review-date">Reviewed: {{ \Carbon\Carbon::parse($verification->reviewed_at)->format('M d, Y') }}</p>
                        @endif
                        @if($verification->remarks)
                            <p class="remarks">{{ $verification->remarks }}</p>
                        @endif
                    </div>
                @endif

                @if(!$verification || $verification->status === 'rejected')
                    <form action="{{ route('shelter.verification.submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group">
                            <label for="registration_doc" class="form-label">Registration Document</label>
                            <div class="file-upload">
                                <input type="file" name="registration_doc" id="registration_doc" class="form-input" accept=".pdf,.jpg,.jpeg,.png" />
                                <p class="file-hint">PDF, JPG, PNG up to 5MB</p>
                            </div>
                            @error('registration_doc')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="facebook_link" class="form-label">Facebook Page Link (Optional)</label>
                            <input type="url" name="facebook_link" id="facebook_link" class="form-input" placeholder="https://facebook.com/your-shelter-page" />
                            @error('facebook_link')
                                <p class="error-text">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn btn-primary">Submit for Verification</button>
                    </form>
                @endif
            </div>
        </div>

        <!-- Password Settings -->
        <div class="settings-card">
            <div class="card-header">
                <h2>Change Password</h2>
            </div>
            <div class="card-content">
                <form>
                    <div class="form-group">
                        <label for="current-password" class="form-label">Current Password</label>
                        <input type="password" id="current-password" class="form-input" />
                    </div>
                    <div class="form-group">
                        <label for="new-password" class="form-label">New Password</label>
                        <input type="password" id="new-password" class="form-input" />
                    </div>
                    <div class="form-group">
                        <label for="confirm-password" class="form-label">Confirm New Password</label>
                        <input type="password" id="confirm-password" class="form-input" />
                    </div>
                    <button type="submit" class="btn btn-primary">Update Password</button>
                </form>
            </div>
        </div>

        <!-- Notification Settings -->
        <div class="settings-card">
            <div class="card-header">
                <h2>Notification Preferences</h2>
            </div>
            <div class="card-content">
                <div class="switch-group">
                    <span class="switch-label">Email Notifications</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-group">
                    <span class="switch-label">Application Updates</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-group">
                    <span class="switch-label">New Pet Alerts</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
                <div class="switch-group">
                    <span class="switch-label">Marketing Communications</span>
                    <label class="switch">
                        <input type="checkbox">
                        <span class="slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Account Deletion -->
        <div class="settings-card">
            <div class="card-header">
                <h2>Account Settings</h2>
            </div>
            <div class="card-content">
                <div class="danger-zone">
                    <h3>Delete Account</h3>
                    <p>Once you delete your account, there is no going back. Please be certain.</p>
                    <button class="btn btn-danger">Delete Account</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function logout() {
        // Here you would typically clear session/local storage
        window.location.href = 'login.html';
    }
</script>

<style>
.verification-status {
    padding: 1rem;
    border-radius: 0.5rem;
    margin-bottom: 1rem;
}

.verification-status.pending {
    background-color: #fff7ed;
    border: 1px solid #fdba74;
}

.verification-status.approved {
    background-color: #f0fdf4;
    border: 1px solid #86efac;
}

.verification-status.rejected {
    background-color: #fef2f2;
    border: 1px solid #fca5a5;
}

.status-text {
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.submission-date, .review-date {
    font-size: 0.875rem;
    color: #6b7280;
}

.remarks {
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: #374151;
}

.file-upload {
    border: 2px dashed #e5e7eb;
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
}

.file-hint {
    font-size: 0.75rem;
    color: #6b7280;
    margin-top: 0.5rem;
}

.error-text {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}
</style>

@endsection