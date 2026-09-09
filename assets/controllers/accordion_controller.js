import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['item', 'panel'];

    toggle(event) {
        const trigger = event.currentTarget;
        const item = trigger.closest('[data-accordion-target="item"]');
        const willOpen = !item.classList.contains('is-open');

        this.itemTargets.forEach((currentItem) => {
            if (currentItem !== item) this.setOpen(currentItem, false);
        });
        this.setOpen(item, willOpen);
    }

    setOpen(item, open) {
        const trigger = item.querySelector('.accordion__trigger');
        const panel = item.querySelector('[data-accordion-target="panel"]');
        item.classList.toggle('is-open', open);
        trigger?.setAttribute('aria-expanded', String(open));
        panel?.setAttribute('aria-hidden', String(!open));
    }
}
