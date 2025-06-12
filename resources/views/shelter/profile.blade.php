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
                    <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t39.30808-6/347439792_262689872915779_1734511534281161924_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEx3xFl7u5jfTV_5eckNDfEgqlXj1z3avOCqVePXPdq89whJ29W46pl6MVM84KD1wjFepXD-UaW6DDSW4eQHod7&_nc_ohc=m_7I_NE9-K0Q7kNvgFi0lNg&_nc_oc=AdiRt7GPOP7QJ-gxFl1lG4A2UBe1eZ6L8UajEeeXX8PUb4BGMftVOv8-jx1oI9sk0LA&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&_nc_gid=ApMVLbMVp0Xh_QdDjSWwWuS&oh=00_AYHRwxYGUVlma7qO1-YvO5im2ZUUEf-Y_wPUtUTpjQBrEg&oe=67D60523" alt="Profile" class="profile-image" />
                    <div class="upload-buttons">
                        <button class="btn btn-outline">Upload New Photo</button>
                        <button class="btn btn-outline">Remove</button>
                    </div>
                </div>

                <form>
                    <label for="name" class="form-label" style="display: flex; align-items: center;">Verified <img src="https://img.icons8.com/ios-filled/50/000000/verified-account.png" alt="Verified Icon" style="width: 24px; height: 24px; margin-left: 0.5rem;"></label>
                    <div class="form-group">
                        <label for="name" class="form-label">Shelter Name</label>
                        <input type="text" id="name" class="form-input" value="Strays Worth Saving" />
                    </div>
                    <div class="form-group">
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">Last Name</label>
                        <input type="text" id="name" class="form-input" value="Raymundo" />
                    </div>
                    <div class="form-group">
                        <label for="name" class="form-label">First Name</label>
                        <input type="text" id="name" class="form-input" value="Andrea Gabbrielle" />
                    </div>
                    <div class="form-group">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" id="email" class="form-input" value="Andrea@gmail.com" />
                    </div>
                    <div class="form-group">
                        <label for="phone" class="form-label">Contact Number</label>
                        <input type="tel" id="phone" class="form-input" value="0912-345-6789" />
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

@endsection