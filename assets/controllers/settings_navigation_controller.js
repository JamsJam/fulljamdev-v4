import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['link'];

    select(event) {
        this.activate(event.currentTarget.dataset.section);
    }

    sync(event) {
        if (event.target.id !== 'settings-section') {
            return;
        }

        const url = new URL(event.target.src || window.location.href, window.location.origin);
        const section = url.pathname.split('/').filter(Boolean).at(-1);

        this.activate(section);
    }

    activate(section) {
        this.linkTargets.forEach((link) => {
            const active = link.dataset.section === section;
            link.classList.toggle('is-active', active);

            if (active) {
                link.setAttribute('aria-current', 'page');
            } else {
                link.removeAttribute('aria-current');
            }
        });
    }
}
