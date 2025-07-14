@extends('layouts.rescuer-profile')

@section('title', "Rescuer's Profile - PawMatch")

@section('rescuer-content')
    <main class="main-content">
        <div class="container">
            <div class="top-bar">
                <div class="welcome-section">
                    <h1>Profile Settings</h1>
                    <p>Update your personal information and manage your account settings here.</p>
                </div>
            </div>
            <div class="settings-grid">
                <!-- Profile Settings -->
                <div class="settings-card">
                    <div class="card-header">
                        <h2>Profile Information</h2>
                    </div>
                    <div class="card-content">
                        <form method="POST" action="{{ route('rescuer.profile.update') }}" enctype="multipart/form-data">
                            @csrf
                            <div class="profile-upload">
                                <img src="{{ $user->profile_image ?? asset('images/default-profile.png') }}" alt="Profile"
                                    class="profile-image" />
                                <div class="upload-buttons">
                                    <input type="file" id="profile_image" name="profile_image" style="display:none;" accept="image/*">
                                    <label for="profile_image" class="btn btn-outline">Upload New Photo</label>
                                    <button type="submit" name="remove_photo" value="1"
                                        class="btn btn-outline">Remove</button>
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="name" class="form-label">Full Name</label>
                                <input type="text" id="name" name="name" class="form-input"
                                    value="{{ $user->first_name }} {{ $user->last_name }}" />
                            </div>
                            <div class="form-group">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email" id="email" name="email" class="form-input"
                                    value="{{ $user->email }}" />
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone_number" class="form-input"
                                    value="{{ $user->phone_number }}" />
                            </div>
                            <div class="form-group">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" id="address" name="address" class="form-input"
                                    value="{{ $rescuer->location ?? '' }}" />
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
                        @if ($verification)
                            <div class="verification-status {{ $verification->status }}">
                                <p class="status-text">Status: {{ ucfirst($verification->status) }}</p>
                                <p class="submission-date">Submitted:
                                    {{ \Carbon\Carbon::parse($verification->submitted_at)->format('M d, Y') }}</p>
                                @if ($verification->reviewed_at)
                                    <p class="review-date">Reviewed:
                                        {{ \Carbon\Carbon::parse($verification->reviewed_at)->format('M d, Y') }}</p>
                                @endif
                                @if ($verification->remarks)
                                    <p class="remarks">{{ $verification->remarks }}</p>
                                @endif
                            </div>
                        @endif

                        @if (!$verification || $verification->status === 'rejected')
                            <form action="{{ route('rescuer.verification.submit') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="form-group">
                                    <label for="registration_doc" class="form-label">Registration Document</label>
                                    <div class="file-upload">
                                        <input type="file" name="registration_doc" id="registration_doc"
                                            class="form-input" accept=".pdf,.jpg,.jpeg,.png" />
                                        <p class="file-hint">PDF, JPG, PNG up to 5MB</p>
                                    </div>
                                    @error('registration_doc')
                                        <p class="error-text">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="form-group">
                                    <label for="facebook_link" class="form-label">Facebook Page Link (Optional)</label>
                                    <input type="url" name="facebook_link" id="facebook_link" class="form-input"
                                        placeholder="https://facebook.com/your-rescuer-page"
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
                        <form method="POST" action="{{ route('rescuer.profile.password') }}">
                            @csrf
                            <div class="form-group">
                                <label for="current-password" class="form-label">Current Password</label>
                                <input type="password" id="current-password" name="current_password" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="new-password" class="form-label">New Password</label>
                                <input type="password" id="new-password" name="new_password" class="form-input" />
                            </div>
                            <div class="form-group">
                                <label for="confirm-password" class="form-label">Confirm New Password</label>
                                <input type="password" id="confirm-password" name="new_password_confirmation"
                                    class="form-input" />
                            </div>
                            <button type="submit" class="btn btn-primary">Update Password</button>
                        </form>
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
                            <form method="POST" action="{{ route('rescuer.profile.delete') }}">
                                @csrf
                                <button type="submit" class="btn btn-danger">Delete Account</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>
    <script>
        function logout() {
            // Here you would typically clear session/local storage
            window.location.href = 'login.html';
        }

        document.getElementById('profile_image').addEventListener('change', function(event) {
            const [file] = event.target.files;
            if (file) {
                const preview = document.querySelector('.profile-image');
                preview.src = URL.createObjectURL(file);
            }
        });
    </script>
@endsection
