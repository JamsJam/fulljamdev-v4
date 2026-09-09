import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    connect() {
        this.element.classList.add('is-prepared');

        this.openFrame = window.requestAnimationFrame(() => {
            if (!this.element.open) {
                this.element.showModal();
            }

            this.element.classList.add('is-visible');
        });
    }

    close() {
        if (this.element.classList.contains('is-closing')) {
            return;
        }

        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.element.close();
            return;
        }

        this.element.classList.add('is-closing');
        this.closeTimeout = window.setTimeout(() => {
            this.element.close();
            this.element.classList.remove('is-closing');
        }, 350);
    }

    closeFromBackdrop(event) {
        if (event.target === this.element) {
            this.close();
        }
    }

    disconnect() {
        if (this.openFrame) {
            window.cancelAnimationFrame(this.openFrame);
        }

        if (this.closeTimeout) {
            window.clearTimeout(this.closeTimeout);
        }
    }
}
