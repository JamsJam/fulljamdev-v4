import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static classes = ['collapsed'];
    static targets = ['trigger', 'overlay'];

    connect() {
        this.collapsed = window.localStorage.getItem('admin-sidebar-collapsed') === 'true';
    }

    toggle() {
        this.collapsed = !this.element.classList.contains(this.collapsedClass);
    }

    set collapsed(value) {
        this.element.classList.toggle(this.collapsedClass, value);

        if (this.hasTriggerTarget) {
            this.triggerTarget.setAttribute('aria-expanded', String(!value));
            this.triggerTarget.setAttribute(
                'aria-label',
                value ? 'Ouvrir le menu latéral' : 'Fermer le menu latéral',
            );
            this.triggerTarget.setAttribute(
                'title',
                value ? 'Ouvrir le menu' : 'Fermer le menu',
            );
        }

        if (this.hasOverlayTarget) {
            this.overlayTarget.classList.toggle('is-visible', !value);
            this.overlayTarget.setAttribute('aria-hidden', String(value));
        }

        window.localStorage.setItem('admin-sidebar-collapsed', String(value));
    }
}
