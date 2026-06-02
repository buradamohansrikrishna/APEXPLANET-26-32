<!-- =========================================
     SKILLSPHERE MODALS COMPONENT
     includes/modals.php
========================================= -->
<div class="modal-overlay" id="confirmModal" tabindex="-1" role="dialog" aria-hidden="true" style="display: none;">
    <div class="modal-card">
        <div class="modal-card__header">
            <h3 id="confirmModalTitle">Are you sure?</h3>
            <button type="button" class="modal-close" onclick="closeConfirmModal()">&times;</button>
        </div>
        <div class="modal-card__body">
            <p id="confirmModalText">This action cannot be undone.</p>
        </div>
        <div class="modal-card__footer">
            <button type="button" class="btn btn-outline" onclick="closeConfirmModal()">Cancel</button>
            <button type="button" class="btn btn-danger" id="confirmModalSubmit">Confirm</button>
        </div>
    </div>
</div>
<script>
function openConfirmModal(title, text, callback) {
    document.getElementById('confirmModalTitle').innerText = title;
    document.getElementById('confirmModalText').innerText = text;
    const submitBtn = document.getElementById('confirmModalSubmit');
    
    // Clear old event listeners
    const newSubmitBtn = submitBtn.cloneNode(true);
    submitBtn.replaceWith(newSubmitBtn);
    
    newSubmitBtn.addEventListener('click', () => {
        callback();
        closeConfirmModal();
    });
    
    document.getElementById('confirmModal').style.display = 'flex';
}
function closeConfirmModal() {
    document.getElementById('confirmModal').style.display = 'none';
}
</script>
