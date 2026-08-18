import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'spinner', 'message'];

    static values = {
        loading: { type: Boolean, default: false },
        url: String,
    };

    connect() {
        this.abortController = null;
    }

    disconnect() {
        this.abortController?.abort();
    }

    async check() {
        this.abortController?.abort();
        const abortController = new AbortController();
        this.abortController = abortController;
        this.loadingValue = true;
        this.clearResult();

        try {
            const url = new URL(this.urlValue, window.location.origin);
            url.searchParams.set('color', this.inputTarget.value);

            const response = await fetch(url, {
                headers: { Accept: 'application/json' },
                signal: abortController.signal,
            });

            if (!response.ok) {
                throw new Error(`Color availability request failed with status ${response.status}`);
            }

            const { available } = await response.json();
            this.showResult(available);
        } catch (error) {
            if ('AbortError' !== error.name) {
                this.showError();
            }
        } finally {
            if (this.abortController === abortController && !abortController.signal.aborted) {
                this.loadingValue = false;
            }
        }
    }

    loadingValueChanged(loading) {
        if (!this.hasSpinnerTarget) {
            return;
        }

        this.spinnerTarget.hidden = !loading;
        this.element.setAttribute('aria-busy', loading.toString());
    }

    clearResult() {
        this.inputTarget.removeAttribute('aria-invalid');
        this.messageTarget.hidden = true;
        this.messageTarget.textContent = '';
        this.messageTarget.classList.remove('is-available', 'is-unavailable');
    }

    showResult(available) {
        this.messageTarget.hidden = false;
        this.messageTarget.textContent = available
            ? 'Cette couleur est disponible.'
            : 'Cette couleur est déjà utilisée.';
        this.messageTarget.classList.add(available ? 'is-available' : 'is-unavailable');
        this.inputTarget.setAttribute('aria-invalid', (!available).toString());
    }

    showError() {
        this.messageTarget.hidden = false;
        this.messageTarget.textContent = 'La vérification de la couleur a échoué.';
        this.messageTarget.classList.add('is-unavailable');
    }
}
