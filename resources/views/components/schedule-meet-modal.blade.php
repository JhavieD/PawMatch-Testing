<div id="schedule-meet-modal-overlay" class="modal-overlay" style="display:none;">
  <div class="modal modal-clean modal-bigger">
    <div class="modal-header-clean">
      <h2 style="margin:0;">Schedule Meet & Greet</h2>
      <button class="close-btn clean-close" onclick="closeScheduleMeetModal()" aria-label="Close">&times;</button>
    </div>
    <form id="schedule-meet-form" class="modal-body-clean">
      <input type="hidden" name="application_id" id="schedule-meet-application-id">
      <div class="modal-section" style="margin-bottom: 1.5rem;">
        <label for="meet-date" class="modal-section-header" style="margin-top:0;">Date</label>
        <input type="date" id="meet-date" name="meet_date" class="form-control" required style="width:100%;padding:0.5rem;margin-top:0.5rem;">
      </div>
      <div class="modal-section" style="margin-bottom: 1.5rem;">
        <label for="meet-time" class="modal-section-header">Time</label>
        <input type="time" id="meet-time" name="meet_time" class="form-control" required style="width:100%;padding:0.5rem;margin-top:0.5rem;">
      </div>
      <div class="modal-section" style="margin-bottom: 2rem;">
        <label for="meet-message" class="modal-section-header">Message (optional)</label>
        <textarea id="meet-message" name="meet_message" class="form-control" rows="3" style="width:100%;padding:0.5rem;margin-top:0.5rem;"></textarea>
      </div>
      <div class="modal-footer-clean">
        <button type="submit" class="btn btn-primary btn-block">Send Request</button>
        <button type="button" class="btn btn-outline btn-block" onclick="closeScheduleMeetModal()">Cancel</button>
      </div>
    </form>
  </div>
</div>

<script>
console.log('Schedule Meet & Greet modal script loaded');
function openScheduleMeetModal(applicationId) {
    document.getElementById('schedule-meet-modal-overlay').style.display = 'flex';
    document.getElementById('schedule-meet-application-id').value = applicationId;
}
function closeScheduleMeetModal() {
    document.getElementById('schedule-meet-modal-overlay').style.display = 'none';
}
function submitScheduleMeet() {
    console.log('submitScheduleMeet called');
    const form = document.getElementById('schedule-meet-form');
    const data = {
        application_id: form.application_id.value,
        meet_date: form.meet_date.value,
        meet_time: form.meet_time.value,
        meet_message: form.meet_message.value,
        _token: '{{ csrf_token() }}'
    };
    console.log('About to send fetch with data:', data);
    fetch('{{ route('adopter.schedule-meet') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(res => {
        console.log('Schedule Meet & Greet response:', res);
        if (res.success) {
            alert('Meet & Greet request submitted!');
            closeScheduleMeetModal();
        } else {
            alert('Error: ' + (res.error || 'Something went wrong.'));
        }
    })
    .catch((err) => { console.log('AJAX error:', err); alert('AJAX error: ' + (err.message || 'Something went wrong.')); });
}
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.querySelector('#schedule-meet-form button[type="submit"]');
    if (btn) {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Send Request button clicked');
            submitScheduleMeet();
        });
    } else {
        console.log('Send Request button not found');
    }
});
</script> 
