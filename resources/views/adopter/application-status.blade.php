@extends('layouts.application-status')

@section('title', 'Application Status - PawMatch')

@section('adopter-content')
<div class="main-content">
    <div class="container">
        <div class="header">
            <h1>My Applications</h1>
            <p>Track the status of your adoption applications</p>

            <!-- Add Tab Navigation -->
            <div class="tab-navigation">
                <button class="tab-btn active" onclick="switchTab('active')">Active Applications</button>
                <button class="tab-btn" onclick="switchTab('completed')">Completed Adoptions</button>
            </div>
        </div>

        <!-- Active Applications Tab -->
        <div id="activeTab" class="tab-content active">
            <!-- Application Card -->
            <div class="application-card">
                <div class="application-header">
                    <div class="pet-info">
                        <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482958427_546890997878558_7939868464340469320_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEg26tmyKvJjnwIVGA4p7VpyQUyRIOYwdHJBTJEg5jB0Wx4wKGdnDZxTVL1IbZ-XSjbaTHznmX8rfBWnCPmJmMd&_nc_ohc=Z4p-MgCZnLUQ7kNvgEaxlgm&_nc_oc=Adj34yEQkoq4BVC1rbv3hqxuOEPtGTzHnNq_bsWvPqyo8vgZ9z2tbbWgJ7HSzT1tE3w&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wGK65XEYIiIyewAECv3ua60cvr7cDuW6l98OwR5gTpdRw&oe=67F7AA9D" alt="Ester" class="pet-image" />
                        <div class="pet-details">
                            <h2>Ester</h2>
                            <p>Tabby Cat • Strays Worth Saving</p>
                        </div>
                    </div>
                    <span class="status status-approved">Application Approved</span>
                </div>

                <div class="application-content">
                    <div class="progress-track">
                        <div class="progress-step">
                            <div class="step-icon completed">✓</div>
                            <div class="step-label">Submitted</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon completed">✓</div>
                            <div class="step-label">Reviewed</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon completed">✓</div>
                            <div class="step-label">Approved</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon">4</div>
                            <div class="step-label">Meet & Greet</div>
                        </div>
                    </div>

                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-date">Today, 2:30 PM</div>
                            <div class="timeline-content">
                                Application approved! Schedule a meet & greet with Ester.
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-date">Yesterday, 4:15 PM</div>
                            <div class="timeline-content">
                                Application review completed by Strays Worth Saving.
                            </div>
                        </li>
                        <li class="timeline-item">
                            <div class="timeline-date">Mar 15, 2024</div>
                            <div class="timeline-content">
                                Application submitted successfully.
                            </div>
                        </li>
                    </ul>

                    <div style="margin-top: 1.5rem;">
                        <a href="#" class="btn btn-outline">Schedule Meet & Greet</a>
                    </div>
                </div>
            </div>

            <!-- Another Application Card -->
            <div class="application-card">
                <div class="application-header">
                    <div class="pet-info">
                        <img src="https://scontent.fmnl17-1.fna.fbcdn.net/v/t1.15752-9/482487994_519954501125001_2078025813899306849_n.jpg?_nc_cat=101&ccb=1-7&_nc_sid=9f807c&_nc_eui2=AeEeZjpdAK_M-yf5h5IvWdXQUR3Ko2en25tRHcqjZ6fbm_KRvQPSzX18tPL-2ParloJBk9AHSq4CfWq5m8dMHjOZ&_nc_ohc=bZ16vY0c35YQ7kNvgGtgcCE&_nc_oc=AdhzMhPeljm5uTxIQuorDQdXH2IQFxYfI27IX1SynAkecUVIN-tpoisUk_G_BIkQA-U&_nc_zt=23&_nc_ht=scontent.fmnl17-1.fna&oh=03_Q7cD1wEaT8XJlz7gNg-5YTKvcnQf3ePKvmU6Pe0I0jDrb0IO1w&oe=67F7B9B4" alt="Fort" class="pet-image" />
                        <div class="pet-details">
                            <h2>Fort</h2>
                            <p>Belgian • Paws & Whiskers Rescue</p>
                        </div>
                    </div>
                    <span class="status status-pending">Under Review</span>
                </div>

                <div class="application-content">
                    <div class="progress-track">
                        <div class="progress-step">
                            <div class="step-icon completed">✓</div>
                            <div class="step-label">Submitted</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon">2</div>
                            <div class="step-label">Reviewed</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon">3</div>
                            <div class="step-label">Approved</div>
                        </div>
                        <div class="progress-step">
                            <div class="step-icon">4</div>
                            <div class="step-label">Meet & Greet</div>
                        </div>
                    </div>

                    <ul class="timeline">
                        <li class="timeline-item">
                            <div class="timeline-date">Mar 14, 2024</div>
                            <div class="timeline-content">
                                Application submitted successfully.
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Completed Adoptions Tab -->
        <div id="completedTab" class="tab-content">
            <div class="application-card">
                <div class="application-header">
                    <div class="pet-info">
                        <img src="https://scontent.fmnl17-2.fna.fbcdn.net/v/t1.15752-9/482393928_1029887379196907_7520748738047288843_n.jpg?_nc_cat=111&ccb=1-7&_nc_sid=0024fc&_nc_eui2=AeE4nSvKW-Jglairq77OJq74nQFUqbRvNRGdAVSptG81ETMMvWLNNNDot6t0cuJqGTVkdN6TlAqL-rHiDFw9sk9i&_nc_ohc=-Tq_Ki9QveIQ7kNvgHFVc2C&_nc_oc=AdiMxKxXS9RMWYaqHvcueY_u4AnrnTNnwhl89ipt65vTRnRgWDITTh1IAbqSj1q6BaQ&_nc_ad=z-m&_nc_cid=0&_nc_zt=23&_nc_ht=scontent.fmnl17-2.fna&oh=03_Q7cD1wEtbHgtAyVOdPJN1mn0s63h65uDad6MJed-fKYld24j0Q&oe=67FBBD10" alt="Ethan" class="pet-image" />
                        <div class="pet-details">
                            <h2>Ethan</h2>
                            <p>Chihuahua • Strays Worth Saving</p>
                        </div>
                    </div>
                    <span class="status status-completed">Adoption Completed</span>
                </div>

                <div class="application-content">
                    <div class="rating-section">
                        <h3>Rate Your Adoption Experience</h3>
                        <div class="star-rating">
                            <span class="star" data-rating="5">★</span>
                            <span class="star" data-rating="4">★</span>
                            <span class="star" data-rating="3">★</span>
                            <span class="star" data-rating="2">★</span>
                            <span class="star" data-rating="1">★</span>
                        </div>
                        <textarea class="review-input" placeholder="Share your adoption experience..."></textarea>
                        <button class="btn btn-primary submit-review">Submit Review</button>
                    </div>
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

    // Tab switching functionality
    function switchTab(tabName) {
        const tabs = document.querySelectorAll('.tab-content');
        const buttons = document.querySelectorAll('.tab-btn');

        tabs.forEach(tab => tab.classList.remove('active'));
        buttons.forEach(btn => btn.classList.remove('active'));

        document.getElementById(tabName + 'Tab').classList.add('active');
        event.target.classList.add('active');
    }

    // Star rating functionality
    document.addEventListener('DOMContentLoaded', function() {
        const starContainer = document.querySelector('.star-rating');
        const stars = document.querySelectorAll('.star');
        let currentRating = 0;

        stars.forEach(star => {
            // Click event
            star.addEventListener('click', function() {
                const rating = this.getAttribute('data-rating');
                currentRating = rating;
                updateStars(rating);
            });
        });

        function updateStars(rating) {
            stars.forEach(star => {
                const starRating = star.getAttribute('data-rating');
                if (starRating <= rating) {
                    star.classList.add('active');
                } else {
                    star.classList.remove('active');
                }
            });
        }

        // Submit review functionality
        document.querySelector('.submit-review').addEventListener('click', function() {
            const review = document.querySelector('.review-input').value;

            if (currentRating === 0) {
                alert('Please select a rating');
                return;
            }

            if (!review.trim()) {
                alert('Please write a review');
                return;
            }

            // Here you would typically send this data to your backend
            console.log('Rating:', currentRating);
            console.log('Review:', review);

            // Reset form
            currentRating = 0;
            updateStars(0);
            document.querySelector('.review-input').value = '';

            alert('Thank you for your review!');
        });
    });
</script>

@endsection