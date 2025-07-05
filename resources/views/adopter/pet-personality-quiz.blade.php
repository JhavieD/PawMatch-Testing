@extends('layouts.pet-personality-quiz')

@section('title', 'Pet Personality Quiz - PawMatch')

@section('adopter-content')
<div class="quiz-container">
    <div class="progress-bar" id="progressBar"></div>
    <div class="questions-wrapper">
        <div class="question-container active" id="question1">
            <div class="question-number">Question 1 of 5</div>
            <div class="question-text">What type of pet would you prefer?</div>
            <div class="options-container">
                <div class="option-card" data-value="dog">
                    <span class="option-icon">🐕</span>
                    <span class="option-text">Dog</span>
                </div>
                <div class="option-card" data-value="cat">
                    <span class="option-icon">🐱</span>
                    <span class="option-text">Cat</span>
                </div>
                <div class="option-card" data-value="either">
                    <span class="option-icon">🤔</span>
                    <span class="option-text">Either</span>
                </div>
            </div>
        </div>
        <div class="question-container" id="question2">
            <div class="question-number">Question 2 of 5</div>
            <div class="question-text">What personality traits are you looking for?</div>
            <div class="options-container">
                <div class="option-card" data-value="calm">
                    <span class="option-icon">😌</span>
                    <span class="option-text">Calm and Relaxed</span>
                </div>
                <div class="option-card" data-value="playful">
                    <span class="option-icon">🎾</span>
                    <span class="option-text">Playful and Energetic</span>
                </div>
                <div class="option-card" data-value="independent">
                    <span class="option-icon">🦁</span>
                    <span class="option-text">Independent</span>
                </div>
                <div class="option-card" data-value="protective">
                    <span class="option-icon">🛡️</span>
                    <span class="option-text">Protective</span>
                </div>
            </div>
        </div>
        <div class="question-container" id="question3">
            <div class="question-number">Question 3 of 5</div>
            <div class="question-text">What's your preferred activity level?</div>
            <div class="options-container">
                <div class="option-card" data-value="low">
                    <span class="option-icon">🌙</span>
                    <span class="option-text">Low - I prefer a relaxed lifestyle</span>
                </div>
                <div class="option-card" data-value="moderate">
                    <span class="option-icon">🌅</span>
                    <span class="option-text">Moderate - Balanced activity</span>
                </div>
                <div class="option-card" data-value="high">
                    <span class="option-icon">⚡</span>
                    <span class="option-text">High - Very active lifestyle</span>
                </div>
            </div>
        </div>
        <div class="question-container" id="question4">
            <div class="question-number">Question 4 of 5</div>
            <div class="question-text">Could you manage a pet with special needs?</div>
            <div class="options-container">
                <div class="option-card" data-value="yes">
                    <span class="option-icon">❤️</span>
                    <span class="option-text">Yes, I'm open to special needs pets</span>
                </div>
                <div class="option-card" data-value="no">
                    <span class="option-icon">🤔</span>
                    <span class="option-text">No, I prefer a healthy pet</span>
                </div>
            </div>
        </div>
        <div class="question-container" id="question5">
            <div class="question-number">Question 5 of 5</div>
            <div class="question-text">What best describes your preferred pet's eating habits?</div>
            <div class="options-container">
                <div class="option-card" data-value="balanced_diet">
                    <span class="option-icon">🥗</span>
                    <span class="option-text">Balanced Diet</span>
                </div>
                <div class="option-card" data-value="portion_control">
                    <span class="option-icon">🍽️</span>
                    <span class="option-text">Portion Control</span>
                </div>
                <div class="option-card" data-value="consistent_schedule">
                    <span class="option-icon">⏰</span>
                    <span class="option-text">Consistent Feeding Schedule</span>
                </div>
            </div>
        </div>
    </div>
    <div class="result-container" id="result">
        <div class="result-icon">🎉</div>
        <h2 class="result-title">Your Perfect Pet Match</h2>
        <div class="result-description" id="resultDescription"></div>
        <div class="navigation-buttons">
            <button class="btn btn-secondary" onclick="restartQuiz()">Take Quiz Again</button>
            <button class="btn" onclick="window.location.href='pet-listings-LoggedIn.html'">Browse Pets</button>
        </div>
    </div>
    <div class="navigation-buttons">
        <button class="btn btn-secondary" id="prevBtn" disabled onclick="prevQuestion()">Previous</button>
        <button class="btn" id="nextBtn" onclick="nextQuestion()" disabled>Next</button>
    </div>
</div>

<script>
    const questions = document.querySelectorAll('.question-container');
    const options = document.querySelectorAll('.option-card');
    const progressBar = document.getElementById('progressBar');
    const resultContainer = document.getElementById('result');
    const resultDescription = document.getElementById('resultDescription');
    const nextBtn = document.getElementById('nextBtn');
    let currentQuestion = 0;
    let answers = {};

    function showQuestion(idx) {
        questions.forEach((q, i) => {
            if (i === idx) {
                q.classList.add('active', 'fade');
                q.style.display = 'flex';
                setTimeout(() => q.classList.add('fade-in'), 10);
            } else {
                q.classList.remove('active', 'fade', 'fade-in');
                q.style.display = 'none';
            }
        });
        // Enable/disable Next button based on answer presence
        const questionId = questions[idx].id;
        if (answers[questionId]) {
            nextBtn.disabled = false;
        } else {
            nextBtn.disabled = true;
        }
    }

    function initQuiz() {
        showQuestion(0);
        updateProgressBar();
        updateNavigationButtons();
    }

    function updateProgressBar() {
        const progress = ((currentQuestion + 1) / questions.length) * 100;
        progressBar.style.width = `${progress}%`;
    }

    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        prevBtn.disabled = currentQuestion === 0;
        nextBtn.textContent = (currentQuestion === questions.length - 1) ? 'Get Results' : 'Next';
    }

    options.forEach(option => {
        option.addEventListener('click', () => {
            const questionContainer = option.closest('.question-container');
            const questionId = questionContainer.id;
            const value = option.dataset.value;
            questionContainer.querySelectorAll('.option-card').forEach(opt => {
                opt.classList.remove('selected');
            });
            option.classList.add('selected');
            answers[questionId] = value;
            // Enable Next button when an option is selected for current question
            if (questions[currentQuestion].id === questionId) {
                nextBtn.disabled = false;
            }
        });
    });

    function nextQuestion() {
        if (currentQuestion === questions.length - 1) {
            showResults();
            return;
        }
        showQuestion(currentQuestion + 1);
        currentQuestion++;
        updateProgressBar();
        updateNavigationButtons();
    }

    function prevQuestion() {
        if (currentQuestion === 0) return;
        showQuestion(currentQuestion - 1);
        currentQuestion--;
        updateProgressBar();
        updateNavigationButtons();
    }

    function showResults() {
        const params = new URLSearchParams(answers).toString();
        window.location.href = "{{ route('adopter.pet-swipe') }}" + '?' + params;
    }

    function restartQuiz() {
        currentQuestion = 0;
        answers = {};
        showQuestion(0);
        resultContainer.classList.remove('active');
        updateProgressBar();
        updateNavigationButtons();
        options.forEach(opt => opt.classList.remove('selected'));
        nextBtn.disabled = true;
    }

    initQuiz();
</script>
@endsection