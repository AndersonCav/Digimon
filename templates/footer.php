    </main>

    <footer class="footer-shell py-3 mt-auto">
        <div class="container text-center text-light">
            <p class="mb-0">&copy; 2026 Digimon Search. Catálogo visual de Digimons com experiência curada em PHP + MySQL + Bootstrap.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const flashSourcesRoot = document.getElementById('flash-toast-sources');
            const flashSources = flashSourcesRoot ? flashSourcesRoot.querySelectorAll('.flash-toast-source') : [];

            if (flashSources.length > 0) {
                const toastContainer = document.createElement('div');
                toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3 flash-toast-container';
                toastContainer.setAttribute('aria-live', 'polite');
                toastContainer.setAttribute('aria-atomic', 'true');

                flashSources.forEach(function (source) {
                    const type = source.getAttribute('data-type') || 'success';
                    const message = source.getAttribute('data-message') || '';
                    const isError = type === 'error';

                    const toast = document.createElement('div');
                    toast.className = 'toast align-items-center border-0 shadow flash-toast ' + (isError ? 'text-bg-danger' : 'text-bg-success');
                    toast.setAttribute('role', 'status');
                    toast.setAttribute('aria-live', 'polite');
                    toast.setAttribute('aria-atomic', 'true');

                    toast.innerHTML =
                        '<div class="d-flex">' +
                            '<div class="toast-body">' + message + '</div>' +
                            '<button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Fechar"></button>' +
                        '</div>';

                    toastContainer.appendChild(toast);
                });

                document.body.appendChild(toastContainer);

                toastContainer.querySelectorAll('.toast').forEach(function (element) {
                    const bsToast = new bootstrap.Toast(element, {
                        autohide: true,
                        delay: 3600
                    });

                    bsToast.show();
                });
            }

            const loadingForm = document.querySelector('[data-loading-form]');
            const loadingState = document.getElementById('search-loading-state');
            const resultsSection = document.getElementById('search-results-section');

            if (loadingForm) {
                loadingForm.addEventListener('submit', function () {
                    const submitButton = loadingForm.querySelector('button[type="submit"]');
                    if (submitButton) {
                        submitButton.classList.add('is-submitting');
                        submitButton.disabled = true;

                        const label = submitButton.querySelector('.btn-label');
                        if (label) {
                            const loadingText = loadingForm.getAttribute('data-loading-text') || 'Processando...';
                            label.textContent = loadingText;
                        }
                    }

                    if (loadingState) {
                        loadingState.classList.remove('d-none');
                    }

                    if (resultsSection) {
                        resultsSection.style.opacity = '0.28';
                        resultsSection.style.pointerEvents = 'none';
                    }
                });
            }

            document.querySelectorAll('[data-favorite-action-form]').forEach(function (form) {
                form.addEventListener('submit', function () {
                    form.classList.add('is-submitting');

                    const button = form.querySelector('button[type="submit"]');
                    if (!button) {
                        return;
                    }

                    button.classList.add('is-submitting');
                    button.disabled = true;

                    const actionInput = form.querySelector('input[name="action"]');
                    const action = actionInput ? actionInput.value : 'add';
                    const label = button.querySelector('.btn-label');

                    if (label) {
                        label.textContent = action === 'remove' ? 'Removendo...' : 'Salvando...';
                    }
                });
            });
        });
    </script>
</body>
</html>