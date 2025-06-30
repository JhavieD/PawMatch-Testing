@extends('layouts.app')

@section('title', 'FAQ - PawMatch')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/shared/public-card.css') }}">
@endpush

@section('content')
<div class="hamburger-spacer"></div>
<div class="public-card-container">
    <div class="public-card-header">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about pet adoption and using PawMatch</p>
    </div>
    <div class="faq-categories-modern">
        <button class="faq-category-btn active" data-category="general">General</button>
        <button class="faq-category-btn" data-category="adoption">Adoption Process</button>
        <button class="faq-category-btn" data-category="shelters">Shelters & Rescuers</button>
    </div>
    <div class="faq-list-modern">
        <!-- General -->
        <div class="faq-category-list" data-category="general">
            <div class="faq-card">
                <div class="faq-question-modern">What is PawMatch?</div>
                <div class="faq-answer-modern">PawMatch is a platform that connects potential pet adopters with shelters and rescuers. We make it easy to:
                    <ul>
                        <li>Browse available pets for adoption</li>
                        <li>Submit adoption applications online</li>
                        <li>Connect with shelters and rescuers</li>
                        <li>Track your adoption journey</li>
                    </ul>
                </div>
            </div>
            <div class="faq-card">
                <div class="faq-question-modern">Is PawMatch free to use?</div>
                <div class="faq-answer-modern">Yes, PawMatch is free for adopters to use! While adoption fees may apply (set by individual shelters), our platform charges no fees to browse pets or submit applications.</div>
            </div>
        </div>
        <!-- Adoption Process -->
        <div class="faq-category-list" data-category="adoption" style="display:none;">
            <div class="faq-card">
                <div class="faq-question-modern">How does the adoption process work?</div>
                <div class="faq-answer-modern">The adoption process typically follows these steps:
                    <ol>
                        <li>Browse available pets and find your match</li>
                        <li>Submit an adoption application</li>
                        <li>Wait for application review by the shelter/rescuer</li>
                        <li>If approved, schedule a meet & greet</li>
                        <li>Complete adoption paperwork and pay any applicable fees</li>
                        <li>Welcome your new family member home!</li>
                    </ol>
                </div>
            </div>
        </div>
        <!-- Shelters & Rescuers -->
        <div class="faq-category-list" data-category="shelters" style="display:none;">
            <div class="faq-card">
                <div class="faq-question-modern">How can shelters join PawMatch?</div>
                <div class="faq-answer-modern">Shelters and rescuers can join by:
                    <ul>
                        <li>Creating a Shelter/Rescuer account</li>
                        <li>Providing organization documentation</li>
                        <li>Completing the verification process</li>
                        <li>Setting up their profile and adding available pets</li>
                    </ul>
                    <p>Contact our <a href="mailto:support@pawmatch.com">support team</a> for assistance.</p>
                </div>
            </div>
        </div>
    </div>
</div>
@push('styles')
<style>
.hamburger-spacer { height: 0; }
@media (max-width: 768px) {
    .hamburger-spacer { height: 3.5rem; }
}
.faq-categories-modern {
    display: flex;
    justify-content: center;
    gap: 1rem;
    margin-bottom: 2rem;
}
.faq-category-btn {
    background: #f3f4f6;
    color: #4a90e2;
    border: none;
    border-radius: 8px;
    padding: 0.6rem 1.3rem;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: background 0.2s, color 0.2s;
}
.faq-category-btn.active, .faq-category-btn:hover {
    background: #4a90e2;
    color: #fff;
}
.faq-list-modern {
    width: 100%;
}
.faq-category-list {
    width: 100%;
}
.faq-card {
    background: #f8fafc;
    border-radius: 12px;
    margin-bottom: 1.1rem;
    box-shadow: 0 1px 4px rgba(74,144,226,0.04);
    padding: 0.2rem 0.7rem;
    transition: box-shadow 0.2s;
}
.faq-question-modern {
    font-size: 1.08rem;
    font-weight: 600;
    color: #222;
    padding: 1rem 0.5rem 1rem 0.5rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: space-between;
    transition: color 0.2s;
}
.faq-question-modern:after {
    content: '\25BC';
    font-size: 1rem;
    color: #4a90e2;
    margin-left: 0.7rem;
    transition: transform 0.2s;
}
.faq-card.active .faq-question-modern:after {
    transform: rotate(180deg);
}
.faq-answer-modern {
    display: none;
    color: #444;
    font-size: 1rem;
    padding: 0 0.5rem 1rem 0.5rem;
    border-left: 3px solid #4a90e2;
    background: #fff;
    border-radius: 0 0 10px 10px;
    margin-top: -0.5rem;
    margin-bottom: 0.5rem;
    animation: fadeIn 0.3s;
}
.faq-card.active .faq-answer-modern {
    display: block;
}
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(-10px); }
    to { opacity: 1; transform: translateY(0); }
}
@media (max-width: 700px) {
    .faq-modern-container {
        max-width: 98vw;
        padding: 0.5rem;
    }
}
</style>
@endpush
@push('scripts')
<script>
// Category filtering
const categoryBtns = document.querySelectorAll('.faq-category-btn');
const categoryLists = document.querySelectorAll('.faq-category-list');
categoryBtns.forEach(btn => {
    btn.addEventListener('click', () => {
        categoryBtns.forEach(b => b.classList.remove('active'));
        btn.classList.add('active');
        const cat = btn.getAttribute('data-category');
        categoryLists.forEach(list => {
            list.style.display = (list.getAttribute('data-category') === cat) ? '' : 'none';
        });
    });
});
// Expand/collapse
const faqQuestions = document.querySelectorAll('.faq-question-modern');
faqQuestions.forEach(q => {
    q.addEventListener('click', () => {
        const card = q.parentElement;
        card.classList.toggle('active');
        // Optionally close others:
        faqQuestions.forEach(otherQ => {
            if (otherQ !== q) otherQ.parentElement.classList.remove('active');
        });
    });
});
</script>
@endpush
@endsection 