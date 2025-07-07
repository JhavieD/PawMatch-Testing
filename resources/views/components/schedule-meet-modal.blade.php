<div id="scheduleMeetModalOverlay" class="modal-overlay {{ isset($active) && $active ? 'active' : '' }}"
    style="display: none;">
    <div id="scheduleMeetModal" class="modal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Schedule Meet & Greet</h5>
                    <button type="button" class="close" aria-label="Close" onclick="closeScheduleMeetModal()">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form id="scheduleMeetForm">
                    <div class="modal-body">
                        <input type="hidden" id="scheduleMeetApplicationId" name="application_id" value="">
                        <div class="form-group">
                            <label for="meetDate">Date</label>
                            <input type="date" class="form-control" id="meetDate" name="date" required>
                        </div>
                        <div class="form-group">
                            <label for="meetTime">Time</label>
                            <input type="time" class="form-control" id="meetTime" name="time" required>
                        </div>
                        <div class="form-group">
                            <label for="meetMessage">Message (optional)</label>
                            <textarea class="form-control" id="meetMessage" name="message" rows="2"
                                placeholder="Add a message for the shelter..."></textarea>
                        </div>
                        <div id="scheduleMeetError" class="alert alert-danger" style="display:none;"></div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary"
                            onclick="closeScheduleMeetModal()">Cancel</button>
                        <button type="submit" class="btn btn-primary">Send Request</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    function openScheduleMeetModalModal(appId) {
        document.getElementById('scheduleMeetApplicationId').value = appId;
        document.getElementById('scheduleMeetForm').reset();
        document.getElementById('scheduleMeetError').style.display = 'none';
        var overlay = document.getElementById('scheduleMeetModalOverlay');
        overlay.classList.add('active');
        overlay.style.display = 'flex';
    }

    function closeScheduleMeetModal() {
        var overlay = document.getElementById('scheduleMeetModalOverlay');
        overlay.classList.remove('active');
        overlay.style.display = 'none';
    }
    // Close modal when clicking outside modal-dialog
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('scheduleMeetModalOverlay').addEventListener('click', function(e) {
            if (e.target === this) closeScheduleMeetModal();
        });
        document.getElementById('scheduleMeetForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            var form = e.target;
            var appId = document.getElementById('scheduleMeetApplicationId').value;
            var date = form.date.value;
            var time = form.time.value;
            var message = form.message.value;
            var errorDiv = document.getElementById('scheduleMeetError');
            errorDiv.style.display = 'none';
            try {
                const response = await fetch('/adopter/schedule-meet', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                            .getAttribute('content')
                    },
                    body: JSON.stringify({
                        application_id: appId,
                        meet_date: date,
                        meet_time: time,
                        meet_message: message
                    })
                });
                if (!response.ok) {
                    const data = await response.json();
                    errorDiv.textContent = data.error || data.message || 'An error occurred.';
                    errorDiv.style.display = 'block';
                    return;
                }
                const data = await response.json();
                if (data && data.redirect_url) {
                    window.location.href = data.redirect_url;
                } else {
                    window.location.href = '/adopter/messages';
                }
            } catch (err) {
                errorDiv.textContent = 'An error occurred. Please try again.';
                errorDiv.style.display = 'block';
            }
        });
    });
</script>
