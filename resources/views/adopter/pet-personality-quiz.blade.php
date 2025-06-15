@extends ('layouts.adopter')

@section('title', 'Pet Personality Quiz')

@section('adopter-content')
 <div class="quiz-container">
        <div class="progress-bar" id="progressBar"></div>
        <div class="questions-wrapper">
            <div class="question-container active" id="question1">
                <div class="question-number">Question 1 of 7</div>
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
                <div class="question-number">Question 2 of 7</div>
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
                <div class="question-number">Question 3 of 7</div>
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
                <div class="question-number">Question 4 of 7</div>
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
                <div class="question-number">Question 5 of 7</div>
                <div class="question-text">Do you have other pets at home?</div>
                <div class="options-container">
                    <div class="option-card" data-value="yes">
                        <span class="option-icon">🐾</span>
                        <span class="option-text">Yes, I have other pets</span>
                    </div>
                    <div class="option-card" data-value="no">
                        <span class="option-icon">🏠</span>
                        <span class="option-text">No, this would be my first pet</span>
                    </div>
                </div>
            </div>

            <div class="question-container" id="question6">
                <div class="question-number">Question 6 of 7</div>
                <div class="question-text">Do you live with children or elderly?</div>
                <div class="options-container">
                    <div class="option-card" data-value="yes">
                        <span class="option-icon">👶</span>
                        <span class="option-text">Yes, I live with children or elderly</span>
                    </div>
                    <div class="option-card" data-value="no">
                        <span class="option-icon">👤</span>
                        <span class="option-text">No, it's just adults</span>
                    </div>
                </div>
            </div>

            <div class="question-container" id="question7">
                <div class="question-number">Question 7 of 7</div>
                <div class="question-text">Do you or anyone in the household have pet allergies?</div>
                <div class="options-container">
                    <div class="option-card" data-value="yes">
                        <span class="option-icon">🤧</span>
                        <span class="option-text">Yes, we have allergies</span>
                    </div>
                    <div class="option-card" data-value="no">
                        <span class="option-icon">😊</span>
                        <span class="option-text">No allergies here</span>
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
            <button class="btn" id="nextBtn" onclick="nextQuestion()">Next</button>
        </div>
    </div>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    const questions = document.querySelectorAll('.question-container');
    const options = document.querySelectorAll('.option-card');
    const progressBar = document.getElementById('progressBar');
    const resultContainer = document.getElementById('result');
    const resultDescription = document.getElementById('resultDescription');
    let currentQuestion = 0;
    let answers = {};

    // Initialize quiz
    function initQuiz() {
        updateProgressBar();
        updateNavigationButtons();
        questions[0].classList.add('active');
    }

    // Update progress bar
    function updateProgressBar() {
        const progress = ((currentQuestion + 1) / questions.length) * 100;
        progressBar.style.width = `${progress}%`;
    }

    // Update navigation buttons
    function updateNavigationButtons() {
        const prevBtn = document.getElementById('prevBtn');
        const nextBtn = document.getElementById('nextBtn');
        
        prevBtn.disabled = currentQuestion === 0;
        
        if (currentQuestion === questions.length - 1) {
            nextBtn.textContent = 'Get Results';
        } else {
            nextBtn.textContent = 'Next';
        }
    }

    // Handle option selection
    options.forEach(option => {
        option.addEventListener('click', () => {
            const questionContainer = option.closest('.question-container');
            const questionId = questionContainer.id;
            const value = option.dataset.value;
            
            // Remove selection from other options in the same question
            questionContainer.querySelectorAll('.option-card').forEach(opt => {
                opt.classList.remove('selected');
            });
            
            // Add selection to clicked option
            option.classList.add('selected');
            
            // Store answer
            answers[questionId] = value;
        });
    });

    // Navigate to next question
    window.nextQuestion = function() {
        if (currentQuestion === questions.length - 1) {
            showResults();
            return;
        }

        const currentContainer = questions[currentQuestion];
        const nextContainer = questions[currentQuestion + 1];
        
        // Prepare next question
        nextContainer.style.visibility = 'visible';
        nextContainer.style.display = 'flex';
        
        // Add transition classes
        currentContainer.classList.add('prev');
        currentContainer.classList.remove('active');
        nextContainer.classList.add('active');
        nextContainer.classList.remove('next');
        
        // Update state
        currentQuestion++;
        updateProgressBar();
        updateNavigationButtons();
        
        // Cleanup after animation
        setTimeout(() => {
            currentContainer.style.visibility = 'hidden';
            questions.forEach(q => {
                if (!q.classList.contains('active')) {
                    q.style.visibility = 'hidden';
                }
            });
        }, 300);
    }

    // Navigate to previous question
    window.prevQuestion = function() {
        const currentContainer = questions[currentQuestion];
        const prevContainer = questions[currentQuestion - 1];
        
        // Prepare previous question
        prevContainer.style.visibility = 'visible';
        prevContainer.style.display = 'flex';
        
        // Add transition classes
        currentContainer.classList.add('next');
        currentContainer.classList.remove('active');
        prevContainer.classList.add('active');
        prevContainer.classList.remove('prev');
        
        // Update state
        currentQuestion--;
        updateProgressBar();
        updateNavigationButtons();
        
        // Cleanup after animation
        setTimeout(() => {
            currentContainer.style.visibility = 'hidden';
            questions.forEach(q => {
                if (!q.classList.contains('active')) {
                    q.style.visibility = 'hidden';
                }
            });
        }, 300);
    }

    // Show results
    function showResults() {
        questions[currentQuestion].classList.remove('active');
        resultContainer.classList.add('active');
        
        // Generate personality-based description
        let description = generatePersonalityDescription(answers);
        resultDescription.innerHTML = description;
    }

    // Restart quiz
    window.restartQuiz = function() {
        currentQuestion = 0;
        answers = {};
        questions.forEach(q => {
            q.classList.remove('active', 'prev', 'next');
        });
        questions[0].classList.add('active');
        resultContainer.classList.remove('active');
        updateProgressBar();
        updateNavigationButtons();
        
        // Clear selections
        options.forEach(opt => opt.classList.remove('selected'));
    }

    // Generate personality description
    function generatePersonalityDescription(answers) {
        let description = "Based on your preferences, your perfect pet match would be ";
        
        if (answers['question1'] === 'either') {
            if (answers['question2'] === 'calm' && answers['question3'] === 'low') {
                description += "a gentle, low-maintenance companion like a senior cat or a small, calm dog breed.";
            } else if (answers['question2'] === 'playful' && answers['question3'] === 'high') {
                description += "an energetic and social pet like a young dog or an active cat breed.";
            } else if (answers['question2'] === 'independent') {
                description += "a self-sufficient pet like a cat or a more independent dog breed.";
            } else {
                description += "a balanced companion that matches your activity level.";
            }
        } else if (answers['question1'] === 'dog') {
            if (answers['question2'] === 'protective') {
                description += "a loyal and protective dog breed that can be both a companion and a guardian.";
            } else if (answers['question2'] === 'playful') {
                description += "an energetic and friendly dog that loves to play and interact.";
            } else {
                description += "a well-mannered dog that fits your lifestyle and activity level.";
            }
        } else {
            if (answers['question2'] === 'independent') {
                description += "a self-sufficient cat that enjoys its own space.";
            } else if (answers['question2'] === 'calm') {
                description += "a gentle and affectionate cat that loves to cuddle.";
            } else {
                description += "a balanced cat that matches your activity level and personality.";
            }
        }

        if (answers['question4'] === 'yes') {
            description += " You're open to special needs pets, which shows great compassion.";
        }

        if (answers['question5'] === 'yes') {
            description += " Since you have other pets, this companion would need to be social and adaptable.";
        }

        if (answers['question6'] === 'yes') {
            description += " Given your living situation with children or elderly, this pet would be gentle and patient.";
        }

        if (answers['question7'] === 'yes') {
            description += " Due to allergies, hypoallergenic breeds would be recommended.";
        }

        return description;
    }

    // Initialize quiz when page loads
    initQuiz();
});
</script>
@endsection