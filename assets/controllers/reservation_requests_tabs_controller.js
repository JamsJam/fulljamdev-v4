import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['tab', 'panel'];

    select(event) {
        const selectedPanel = event.currentTarget.getAttribute('aria-controls');

        this.tabTargets.forEach((tab) => {
            const isSelected = tab === event.currentTarget;
            tab.setAttribute('aria-selected', String(isSelected));
            tab.tabIndex = isSelected ? 0 : -1;
        });

        this.panelTargets.forEach((panel) => {
            panel.hidden = panel.id !== selectedPanel;
        });
    }
}
