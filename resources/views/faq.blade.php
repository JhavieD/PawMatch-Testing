@extends('layouts.app')

@section('title', 'FAQ - PawMatch')

@section('content')
<div id="faq">
    <div class="faq-content">
        <h1>Frequently Asked Questions</h1>
        <p>Find answers to common questions about pet adoption and using PawMatch</p>
        <div class="faq-categories">
            <button class="category-btn active">General</button>
            <button class="category-btn">Adoption Process</button>
            <button class="category-btn">Shelters & Rescuers</button>
        </div>
        <div class="faq-section">
            <!-- General Questions -->
            <div class="faq-item">
                <div class="faq-question">
                    <div class="question-text">What is PawMatch?</div>
                    <div class="toggle-icon">+</div>
                </div>
                <div class="faq-answer hidden">
                    <p>PawMatch is a platform that connects potential pet adopters with shelters and rescuers. We make it easy to:</p>
                    <ul>
                        <li>Browse available pets for adoption</li>
                        <li>Submit adoption applications online</li>
                        <li>Connect with shelters and rescuers</li>
                        <li>Track your adoption journey</li>
                    </ul>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <div class="question-text">Is PawMatch free to use?</div>
                    <div class="toggle-icon">+</div>
                </div>
                <div class="faq-answer hidden">
                    <p>Yes, PawMatch is free for adopters to use! While adoption fees may apply (set by individual shelters), our platform charges no fees to browse pets or submit applications.</p>
                </div>
            </div>
            <div class="faq-item">
                <div class="faq-question">
                    <div class="question-text">How does the adoption process work?</div>
                    <div class="toggle-icon">+</div>
                </div>
                <div class="faq-answer hidden">
                    <p>The adoption process typically follows these steps:</p>
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
            <div class="faq-item">
                <div class="faq-question">
                    <div class="question-text">How can shelters join PawMatch?</div>
                    <div class="toggle-icon">+</div>
                </div>
                <div class="faq-answer hidden">
                    <p>Shelters and rescuers can join by:</p>
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
    <link rel="stylesheet" href="{{ asset('css/marketing.css') }}">
</div>
@push('scripts')
<script>
    document.querySelectorAll('.faq-question').forEach(question => {
        question.addEventListener('click', () => {
            const answer = question.nextElementSibling;
            const icon = question.querySelector('.toggle-icon');
            answer.classList.toggle('hidden');
            icon.textContent = answer.classList.contains('hidden') ? '+' : '-';
        });
    });
    document.querySelectorAll('.category-btn').forEach(button => {
        button.addEventListener('click', () => {
            document.querySelectorAll('.category-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            button.classList.add('active');
        });
    });
</script>
@endpush
@endsection 