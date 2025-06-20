@extends('layouts.adopter')

@section('title', 'Adopter Dashboard - PawMatch')

@section('adopter-content')

<main class="main-content">
    <!-- Centering Wrapper -->
    <div class="content-wrapper">
        <!-- Top Bar -->
        <div class="top-bar">
            <div class="welcome-section">
                <h1>Welcome, {{ $user->first_name ?? 'User' }}!</h1>
                <p>Here's what's happening with your pet adoption journey</p>
            </div>
            <div class="profile-section">
            <div class="profile-img">
                <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}" style="width: 100%; height: 100%; border-radius: 50%;" />
            </div>
            <div class="profile-info">
                    <strong>{{ $user->first_name }} {{ $user->last_name }}</strong>
                </div>
            </div>
        </div>

        <div class="content-grid">
            <!-- Favorite Pets -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Favorite Pets</h2>
                    <a href="pet-listings-LoggedIn.html" class="btn btn-outline">Find More</a>
                </div>
                <div class="pet-grid">
                    <div class="pet-card">
                        <img
                            src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D"
                            alt="Dog"
                            class="pet-image" />
                        <div class="pet-info">
                            <div class="pet-name">Ester</div>
                            <div class="pet-details">Tabby Cat • 2 years</div>
                        </div>
                    </div>
                    <div class="pet-card">
                        <img
                            src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482487994_519954501125001_2078025813899306849_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEeZjpdAK_M-yf5h5IvWdXQUR3Ko2en25tRHcqjZ6fbm_KRvQPSzX18tPL-2ParloJBk9AHSq4CfWq5m8dMHjOZ&_nc_ohc=bZ16vY0c35YQ7kNvgGtgcCE&_nc_oc=AdhzMhPeljm5uTxIQuorDQdXH2IQFxYfI27IX1SynAkecUVIN-tpoisUk_G_BIkQA-U&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wEaT8XJlz7gNg-5YTKvcnQf3ePKvmU6Pe0I0jDrb0IO1w&oe=67F7B9B4"
                            alt="Cat"
                            class="pet-image" />
                        <div class="pet-info">
                            <div class="pet-name">Fort</div>
                            <div class="pet-details">Belgian • 1 year</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Applications -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Applications</h2>
                    <a href="application-status.html" class="btn btn-outline">View All</a>
                </div>
                <ul class="application-list">
                    <li class="application-item">
                        <div class="pet-name">Ester - Tabby Cat</div>
                        <div class="pet-details">Strays Worth Saving</div>
                        <span class="status status-pending">Application Pending</span>
                    </li>
                    <li class="application-item">
                        <div class="pet-name">Fort - Belgian </div>
                        <div class="pet-details">Paws & Whiskers Rescue</div>
                        <span class="status status-approved">Approved - Schedule Visit</span>
                    </li>
                </ul>
            </div>

            <!-- Recent Messages -->
            <div class="content-card">
                <div class="card-header">
                    <h2>Recent Messages</h2>
                    <a href="messages-adopter.html" class="btn btn-outline">View All</a>
                </div>
                <ul class="message-list">
                    <li class="message-item">
                        <div class="message-header">
                            <span class="message-sender">Strays Worth Saving</span>
                            <span class="message-time">2:30 PM</span>
                        </div>
                        <div class="message-preview">
                            Thank you for your interest in Ester! We'd be happy to schedule a
                            visit for you to meet him. Please let us know what time works best
                            for you.
                        </div>
                    </li>
                    <li class="message-item">
                        <div class="message-header">
                            <span class="message-sender">Paws & Whiskers Rescue</span>
                            <span class="message-time">Yesterday</span>
                        </div>
                        <div class="message-preview">
                            Your application for Fort has been approved! Let's schedule a meet
                            and greet at your earliest convenience.
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- .content-wrapper -->
</main>
@endsection