<script>
document.addEventListener('DOMContentLoaded', function () {
    if (window.__submitLoadingGuardInitialized) {
        return;
    }

    window.__submitLoadingGuardInitialized = true;

    function resolveLoadingText(button) {
        const explicitText = button.getAttribute('data-loading-text');
        if (explicitText) {
            return explicitText;
        }

        const label = (button.textContent || '').replace(/\s+/g, ' ').trim().toLowerCase();
        const icon = '<i class="fas fa-spinner fa-spin"></i>';

        if (label.includes('proses upgrade')) {
            return icon + ' Memproses Upgrade...';
        }

        if (label.includes('tandai dibayar')) {
            return icon + ' Memproses Pembayaran...';
        }

        if (label.includes('simpan')) {
            return icon + ' Menyimpan...';
        }

        if (label.includes('update') || label.includes('ubah')) {
            return icon + ' Memperbarui...';
        }

        if (label.includes('hapus')) {
            return icon + ' Menghapus...';
        }

        if (label.includes('batalkan')) {
            return icon + ' Membatalkan...';
        }

        if (label.includes('posting')) {
            return icon + ' Memposting...';
        }

        if (label.includes('kirim')) {
            return icon + ' Mengirim...';
        }

        return icon + ' Memproses...';
    }

    const forms = document.querySelectorAll('form');

    forms.forEach(function (form) {
        const method = (form.getAttribute('method') || 'GET').toUpperCase();

        // Ignore filter/search forms that use GET.
        if (method === 'GET') {
            return;
        }

        let isSubmitting = false;

        form.addEventListener('submit', function (event) {
            if (isSubmitting) {
                event.preventDefault();
                return;
            }

            isSubmitting = true;

            const submitButtons = form.querySelectorAll('button[type="submit"], input[type="submit"]');

            submitButtons.forEach(function (button) {
                if (button.disabled) {
                    return;
                }

                if (button.tagName === 'BUTTON') {
                    const defaultLabel = button.querySelector('.submit-label-default');
                    const loadingLabel = button.querySelector('.submit-label-loading');

                    if (defaultLabel && loadingLabel) {
                        defaultLabel.style.display = 'none';
                        loadingLabel.style.display = 'inline';
                    } else {
                        const loadingText = resolveLoadingText(button);
                        if (!button.dataset.originalHtml) {
                            button.dataset.originalHtml = button.innerHTML;
                        }
                        button.innerHTML = loadingText;
                    }
                } else {
                    const loadingValue = button.getAttribute('data-loading-text') || 'Memproses...';
                    if (!button.dataset.originalValue) {
                        button.dataset.originalValue = button.value;
                    }
                    button.value = loadingValue;
                }

                button.disabled = true;
                button.classList.add('disabled');
            });
        });
    });
});
</script>