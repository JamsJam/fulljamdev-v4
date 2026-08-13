import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['tab', 'panel'];
    static values = { defaultValue: String };

    connect() {
        const initial = this.defaultValueValue || this.tabTargets.find((tab) => tab.getAttribute('aria-selected') === 'true')?.dataset.tab;

        if (initial) {
            this.selectValue(initial);
        }
    }

    select(event) {
        this.selectValue(event.currentTarget.dataset.tab);
    }

    selectValue(selected) {

        this.tabTargets.forEach((tab) => {
            const active = tab.dataset.tab === selected;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', String(active));
            tab.tabIndex = active ? 0 : -1;
        });

        this.panelTargets.forEach((panel) => {
            panel.hidden = panel.dataset.panel !== selected;
        });
    }
}
