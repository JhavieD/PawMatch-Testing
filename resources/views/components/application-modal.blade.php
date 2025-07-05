<div id="application-modal-overlay" class="modal-overlay" style="display:none;">
  <div class="modal modal-clean modal-bigger">
    <div class="modal-header-clean">
      <div class="modal-header-left">
        <img id="modal-pet-image" src="" alt="Pet" />
        <div>
          <h2 id="modal-pet-name">Pet Name</h2>
          <p id="modal-shelter-name">Shelter Name</p>
          <span id="modal-status-badge" class="status-badge">Status</span>
        </div>
      </div>
      <button class="close-btn clean-close" onclick="closeApplicationModal()" aria-label="Close">&times;</button>
    </div>
    <div class="modal-body-clean">
      <h3 class="modal-section-header">Progress</h3>
      <div id="modal-progress-tracker" class="modal-section" style="margin-bottom: 1.5rem;"></div>
      <h3 class="modal-section-header">Timeline</h3>
      <div id="modal-timeline" class="modal-section" style="margin-bottom: 1.5rem;"></div>
      <h3 class="modal-section-header">Adopter Answers</h3>
      <div id="modal-application-details" class="modal-section" style="margin-bottom: 2rem;"></div>
    </div>
    <div class="modal-footer-clean">
      <button id="modal-action-btn" class="btn btn-primary btn-block" style="display:none;">Schedule Meet & Greet</button>
    </div>
  </div>
</div> 