import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['menu', 'trigger'];

    connect() {
        this.closeOnOutsideClick = this.closeOnOutsideClick.bind(this);
        this.closeOnEscape = this.closeOnEscape.bind(this);
        this.closeOnViewportChange = this.closeOnViewportChange.bind(this);
        this.closeOnOtherDropdownOpen = this.closeOnOtherDropdownOpen.bind(this);
        document.addEventListener('click', this.closeOnOutsideClick);
        document.addEventListener('keydown', this.closeOnEscape);
        document.addEventListener('dropdown-menu:open', this.closeOnOtherDropdownOpen);
        window.addEventListener('resize', this.closeOnViewportChange);
        window.addEventListener('scroll', this.closeOnViewportChange, true);
    }

    disconnect() {
        document.removeEventListener('click', this.closeOnOutsideClick);
        document.removeEventListener('keydown', this.closeOnEscape);
        document.removeEventListener('dropdown-menu:open', this.closeOnOtherDropdownOpen);
        window.removeEventListener('resize', this.closeOnViewportChange);
        window.removeEventListener('scroll', this.closeOnViewportChange, true);
    }

    toggle(event) {
        event.stopPropagation();
        const willOpen = this.menuTarget.hidden;

        if (willOpen) {
            document.dispatchEvent(new CustomEvent('dropdown-menu:open', {
                detail: { element: this.element },
            }));
        }

        this.menuTarget.hidden = !willOpen;
        this.triggerTarget.setAttribute('aria-expanded', String(willOpen));

        if (willOpen) {
            this.positionMenu();
            this.menuTarget.querySelector('[role="menuitem"]')?.focus();
        }
    }

    close() {
        this.menuTarget.hidden = true;
        this.triggerTarget.setAttribute('aria-expanded', 'false');
    }

    closeOnOutsideClick(event) {
        if (!this.menuTarget.hidden && !this.element.contains(event.target)) {
            this.close();
        }
    }

    closeOnEscape(event) {
        if ('Escape' === event.key && !this.menuTarget.hidden) {
            this.close();
            this.triggerTarget.focus();
        }
    }

    closeOnViewportChange() {
        if (!this.menuTarget.hidden) {
            this.close();
        }
    }

    closeOnOtherDropdownOpen(event) {
        if (event.detail.element !== this.element && !this.menuTarget.hidden) {
            this.close();
        }
    }

    positionMenu() {
        const spacing = 8;
        const viewportPadding = 8;
        const trigger = this.triggerTarget.getBoundingClientRect();
        const menu = this.menuTarget.getBoundingClientRect();
        const alignStart = this.element.classList.contains('dropdown-menu--start');

        let left = alignStart ? trigger.left : trigger.right - menu.width;
        left = Math.max(viewportPadding, Math.min(left, window.innerWidth - menu.width - viewportPadding));

        let top = trigger.bottom + spacing;
        if (top + menu.height > window.innerHeight - viewportPadding) {
            top = Math.max(viewportPadding, trigger.top - menu.height - spacing);
        }

        this.menuTarget.style.left = `${left}px`;
        this.menuTarget.style.top = `${top}px`;
    }
}
