@props(['bulkId' => 'admin-bulk-form'])

@once
@push('scripts')
<script>
window.adminTableUpdateBulk = function(bulkId) {
    const checkboxes = document.querySelectorAll('.admin-row-checkbox[data-bulk-id="' + bulkId + '"]');
    const checked = document.querySelectorAll('.admin-row-checkbox[data-bulk-id="' + bulkId + '"]:checked');
    const bar = document.getElementById(bulkId + '-bar');
    const count = document.getElementById(bulkId + '-count');
    const selectAll = document.getElementById(bulkId + '-select-all');

    if (count) count.textContent = checked.length;
    if (bar) bar.classList.toggle('hidden', checked.length === 0);
    if (selectAll) {
        selectAll.checked = checkboxes.length > 0 && checked.length === checkboxes.length;
        selectAll.indeterminate = checked.length > 0 && checked.length < checkboxes.length;
    }

    // Sync checked ids into bulk form if they live outside the form
    const form = document.getElementById(bulkId);
    if (form) {
        form.querySelectorAll('input[data-synced-checkbox]').forEach(el => el.remove());
        checked.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = cb.name;
            input.value = cb.value;
            input.setAttribute('data-synced-checkbox', '1');
            form.appendChild(input);
        });
    }
};

window.adminTableClearSelection = function(bulkId) {
    document.querySelectorAll('.admin-row-checkbox[data-bulk-id="' + bulkId + '"]').forEach(cb => cb.checked = false);
    const selectAll = document.getElementById(bulkId + '-select-all');
    if (selectAll) {
        selectAll.checked = false;
        selectAll.indeterminate = false;
    }
    window.adminTableUpdateBulk(bulkId);
};

document.addEventListener('change', function(e) {
    if (e.target.classList.contains('admin-select-all')) {
        const bulkId = e.target.dataset.bulkId;
        document.querySelectorAll('.admin-row-checkbox[data-bulk-id="' + bulkId + '"]').forEach(cb => {
            cb.checked = e.target.checked;
        });
        window.adminTableUpdateBulk(bulkId);
    }
    if (e.target.classList.contains('admin-row-checkbox')) {
        window.adminTableUpdateBulk(e.target.dataset.bulkId);
    }
});
</script>
@endpush
@endonce
