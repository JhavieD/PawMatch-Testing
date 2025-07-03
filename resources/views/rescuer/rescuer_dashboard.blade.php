@extends('layouts.rescuer')

@section('title', 'Rescuer Dashboard - PawMatch')

@section('rescuer-content')

    <main class="rescuer-main-content">
        <div class="rescuer-content-wrapper">
            <!-- Profile and Add Pet Button at Top Right -->
            <div class="dashboard-profile-actions">
                <img src="{{ auth()->user()->profile_image ?? asset('images/default-profile.png') }}" alt="Profile Picture" class="profile-img" />
                <button class="btn btn-primary add-pet-btn">+ Add New Pet</button>
                
    <!-- Main Content -->
    <main class="main-content">
        <!-- Centering Wrapper -->
        <div class="content-wrapper">
            <!-- Top Bar -->
            <div class="top-bar">
                <div class="welcome-section">
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold">
                            {{ $rescuer->organization_name ?? 'Rescuer' }}
                        </h1>

                        <!-- Work in Progress -->
                        @if($verification && $verification->status === 'approved')
                        <div class="verification-badge approved" title="Verified rescuer">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 6.293a1 1 0 00-1.414 0L9 12.586 
                                        6.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 
                                        001.414 0l7-7a1 1 0 000-1.414z" 
                                    clip-rule="evenodd" />
                            </svg>
                        </div>
                        @else
                        <a href="{{ route('rescuer.verification.form') }}"
                            class="verification-badge unverified"
                            title="Click to verify your rescuer profile">
                            <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd"
                                    d="M16.707 6.293a1 1 0 00-1.414 0L9 12.586 
                                        6.707 10.293a1 1 0 00-1.414 1.414l3 3a1 1 0 
                                        001.414 0l7-7a1 1 0 000-1.414z" 
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        @endif
                        </div>
                            <p>Welcome back! Here's what's happening at your rescue group</p>
                        </div>
            <!-- Work in Progress -->
            
                <div class="profile-section">
                    <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t39.30808-6/347439792_262689872915779_1734511534281161924_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=6ee11a&_nc_eui2=AeEx3xFl7u5jfTV_5eckNDfEgqlXj1z3avOCqVePXPdq89whJ29W46pl6MVM84KD1wjFepXD-UaW6DDSW4eQHod7&_nc_ohc=m_7I_NE9-K0Q7kNvgFi0lNg&_nc_oc=AdiRt7GPOP7QJ-gxFl1lG4A2UBe1eZ6L8UajEeeXX8PUb4BGMftVOv8-jx1oI9sk0LA&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&_nc_gid=ApMVLbMVp0Xh_QdDjSWwWuS&oh=00_AYHRwxYGUVlma7qO1-YvO5im2ZUUEf-Y_wPUtUTpjQBrEg&oe=67D60523"
                        alt="Profile Picture" class="profile-img" />
                    <button class="btn btn-primary add-pet-btn">+ Add New Pet</button>
                </div>

            </div>
            <!-- Top Row: Welcome (left) and Available Pets (right) -->
            <div class="dashboard-top-row">
                <div class="dashboard-welcome-card">
                    <div class="flex items-center space-x-2">
                        <h1 class="text-2xl font-bold">Hi, {{ $rescuer->organization_name ?? 'Rescuer' }}!</h1>
                    </div>
                    <p>Welcome back! Here's what's happening at your rescue</p>
                </div>
                <div class="available-pets-card">
                    <div class="stat-number">{{ $availablePets }}</div>
                    <div class="stat-label">Available Pets</div>
                    <div class="stat-icon">🐾</div>
                </div>
            </div>
            <!-- Stats Grid (other stats) -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-header"><div class="stat-icon">📝</div></div>
                    <div class="stat-number">{{ $pendingApplications }}</div>
                    <div class="stat-label">Pending Applications</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><div class="stat-icon">✨</div></div>
                    <div class="stat-number">{{ $successfulAdoptions }}</div>
                    <div class="stat-label">Successful Adoptions</div>
                </div>
                <div class="stat-card">
                    <div class="stat-header"><div class="stat-icon">💌</div></div>
                    <div class="stat-number">{{ $newMessages }}</div>
                    <div class="stat-label">New Messages</div>
                </div>
                <div class="stat-card rating-card">
                    <div class="stat-header"><div class="stat-icon">⭐</div></div>
                    <div class="rating-display">
                        <div class="rating-number">
                            @if($averageRating == 0)
                            0
                            @else
                            {{ number_format($averageRating, 1) }}
                            @endif
                        </div>
                        <div class="star-display">
                            @for ($i = 0; $i < 5; $i++)
                                <span style="color: #fbbf24;">
                                    {{ $i < round($averageRating) ? '★' : '☆' }}
                                </span>
                            @endfor
                        </div>
                        <div class="total-reviews">{{ $totalReviews }}</div>
                    </div>
                </div>
            </div>

            <div class="content-grid">
                <!-- Pet Management -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Pets</h2>
                        <a href="{{ route('rescuer.pet-management') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="pet-list">
                        @forelse ($recentPets->take(2) as $pet)
                            <li class="pet-item">
                                <img src="{{ $pet->image_url ?? 'https://placehold.co/60x60' }}" alt="{{ $pet->name }}"
                                    class="pet-image-small">
                                <div class="pet-info">
                                    <div class="pet-name">{{ $pet->name }}</div>
                                    <div class="pet-details">{{ $pet->breed }} • {{ $pet->age }} years</div>
                                    <span class="status status-{{ strtolower($pet->adoption_status) }}">
                                        {{ ucfirst($pet->adoption_status) }}
                                    </span>
                                </div>
                            </li>
                        @empty
                            <li>No Recent Pets.</li>
                        @endforelse
                    </ul>
                </div>
                <!-- Recent Applications -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Applications</h2>
                        <a href="{{ route('rescuer.pet_applications') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentApplications->take(2) as $app)
                            <li class="application-item">
                                <div class="applicant-info">
                                    <strong>{{ $app->adopter->user->first_name ?? 'Applicant' }}</strong> applied to
                                    adopt <strong>{{ $app->pet->name ?? 'Pet' }}</strong>
                                </div>
                                <div class="pet-details">{{ $app->created_at->diffForHumans() }}</div>
                                <div class="btn-group" style="margin-top: 0.5rem;">
                                    <button class="btn btn-primary"
                                        onclick="showApplicationModal({{ $app->application_id }})">Review
                                        Application</button>
                                    <button
                                        onclick="window.location.href= '{{ route('shelter.messages', ['receiver_id' => $app->adopter->user->user_id]) }}'"
                                        class="btn btn-outline">Message</button>
                                </div>
                            </li>
                        @empty
                            <li>No recent Applications</li>
                        @endforelse
                    </ul>
                </div>
                <!-- Recent Messages -->
                <div class="content-card">
                    <div class="card-header">
                        <h2>Recent Messages</h2>
                        <a href="{{ route('rescuer.messages') }}" class="btn btn-outline">View All</a>
                    </div>
                    <ul class="application-list">
                        @forelse($recentMessages->take(2) as $msg)
                            <li class="application-item">
                                <div class="applicant-info">
                                    <strong>{{ $msg->sender->name ?? 'User' }}</strong>
                                    @if (isset($msg->pets))
                                        <div class="pet-details">Re: {{ $msg->pets->name }} - {{ $msg->pet->breed }}
                                        </div>
                                    @endif
                                </div>
                                <div class="pet-details">
                                    {{ Str::limit($msg->content, 60) }}
                                </div>
                                <button
                                    onclick="window.location.href='{{ route('rescuer.messages', ['receiver_id' => $msg->sender->user_id]) }}'"
                                    class="btn btn-outline" style="margin-top: 0.5rem;">
                                    Reply
                                </button>
                            </li>
                        @empty
                            <li>No recent Messages</li>
                        @endforelse
                    </ul>
                </div>
            </div><!-- .content-grid -->

            <div class="content-card" style="margin-top: 2rem;">
                <div class="card-header">
                    <h2>Recent Reviews</h2>
                </div>
                <div class="reviews-list">
                    @forelse($recentReviews as $review)
                        <div class="review-card">
                            <div class="review-header">
                                <div class="reviewer-info">
                                    <img src="{{ $review->user->profile_picture ?? 'https://placehold.co/40x40' }}"
                                        alt="Reviewer" class="reviewer-image">
                                    <div>
                                        <div class="reviewer-name">{{ $review->user->name ?? 'User' }}</div>
                                        <div class="review-date">{{ $review->created_at->format('F d, Y') }}</div>
                                    </div>
                                </div>
                                <div class="review-rating">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        ★
                                    @endfor
                                </div>
                            </div>
                        </div>
                        <div class="review-content">
                            "{{ $review->comment }}"
                        </div>
                    @empty
                        <div>No recent reviews</div>
                    @endforelse
                </div>
            </div>
        </div><!-- .rescuer-content-wrapper -->
    </main>
@endsection
