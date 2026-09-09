import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static classes = ['open'];
    static targets = ['button', 'submenu'];
    static values = { open: Boolean };

    connect() {
        this.render();
    }

    toggle() {
        this.openValue = !this.openValue;
    }

    openValueChanged() {
        this.render();
    }

    render() {
        if (!this.hasButtonTarget || !this.hasSubmenuTarget) {
            return;
        }

        this.element.classList.toggle(this.openClass, this.openValue);
        this.buttonTarget.setAttribute('aria-expanded', String(this.openValue));
        this.submenuTarget.inert = !this.openValue;
    }
}
