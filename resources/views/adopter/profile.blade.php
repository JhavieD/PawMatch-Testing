@extends('layouts.adopter')

@section('title', "Adopter's Profile - PawMatch")

@section('adopter-content')
<main class="main-content">
    <div class="container adopter-profile-container">
        <div class="header adopter-profile-header">
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
                    <form method="POST" action="{{ route('adopter.profile.update') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="profile-upload">
                            <img src="{{ $user->profile_image ?? asset('images/default-profile.png') }}" alt="Profile" class="profile-image" />
                            <div class="upload-buttons">
                                <input type="file" name="profile_image" id="profile_image" class="profile-image-input">
                                <label for="profile_image" class="btn btn-outline">Upload New Photo</label>
                                <button type="submit" name="remove_photo" value="1" class="btn btn-outline">Remove</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input" value="{{ $user->first_name }} {{ $user->last_name }}" />
                        </div>
                        <div class="form-group">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" value="{{ $user->email }}" />
                        </div>
                        <div class="form-group">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" id="phone" name="phone_number" class="form-input" value="{{ $user->phone_number }}" />
                        </div>
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-input" value="{{ $adopter->address ?? '' }}" />
                        </div>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </form>
                </div>
            </div>
            <!-- Password Settings -->
            <div class="settings-card">
                <div class="card-header">
                    <h2>Change Password</h2>
                </div>
                <div class="card-content">
                    <form method="POST" action="{{ route('adopter.profile.password') }}">
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
                            <input type="password" id="confirm-password" name="new_password_confirmation" class="form-input" />
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
                    <form method="POST" action="{{ route('adopter.profile.notifications') }}">
                        @csrf
                        <div class="switch-group">
                            <span class="switch-label">Email Notifications</span>
                            <label class="switch">
                                <input type="checkbox" name="email_notifications" {{ $adopter->email_notifications ?? true ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-group">
                            <span class="switch-label">Application Updates</span>
                            <label class="switch">
                                <input type="checkbox" name="application_updates" {{ $adopter->application_updates ?? true ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-group">
                            <span class="switch-label">New Pet Alerts</span>
                            <label class="switch">
                                <input type="checkbox" name="new_pet_alerts" {{ $adopter->new_pet_alerts ?? false ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <div class="switch-group">
                            <span class="switch-label">Marketing Communications</span>
                            <label class="switch">
                                <input type="checkbox" name="marketing_communications" {{ $adopter->marketing_communications ?? false ? 'checked' : '' }}>
                                <span class="slider"></span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary">Save Preferences</button>
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
                        <form method="POST" action="{{ route('adopter.profile.delete') }}">
                            @csrf
                            <button type="submit" class="btn btn-danger">Delete Account</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

