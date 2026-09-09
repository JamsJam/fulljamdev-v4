import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = [
        'allCheckbox',
        'dropdown',
        'empty',
        'event',
        'filterLabel',
        'planningCheckbox',
        'trigger',
    ];

    connect() {
        this.closeOnOutsideClick = this.closeOnOutsideClick.bind(this);
        document.addEventListener('click', this.closeOnOutsideClick);
        this.filter();
    }

    disconnect() {
        document.removeEventListener('click', this.closeOnOutsideClick);
    }

    toggleDropdown(event) {
        event.stopPropagation();
        const willOpen = this.dropdownTarget.hidden;

        this.dropdownTarget.hidden = !willOpen;
        this.triggerTarget.setAttribute('aria-expanded', String(willOpen));
    }

    closeOnOutsideClick(event) {
        if (!this.dropdownTarget.hidden && !this.dropdownTarget.contains(event.target) && !this.triggerTarget.contains(event.target)) {
            this.closeDropdown();
        }
    }

    toggleAll() {
        this.planningCheckboxTargets.forEach((checkbox) => {
            checkbox.checked = this.allCheckboxTarget.checked;
        });
        this.filter();
    }

    filter() {
        const selectedPlanningIds = this.planningCheckboxTargets
            .filter((checkbox) => checkbox.checked)
            .map((checkbox) => checkbox.value);

        this.eventTargets.forEach((event) => {
            event.hidden = !selectedPlanningIds.includes(event.dataset.planningId);
        });

        const selectedCount = selectedPlanningIds.length;
        const planningCount = this.planningCheckboxTargets.length;
        this.allCheckboxTarget.checked = selectedCount === planningCount;
        this.allCheckboxTarget.indeterminate = selectedCount > 0 && selectedCount < planningCount;
        this.filterLabelTarget.textContent = selectedCount === planningCount
            ? 'Planning'
            : selectedCount === 0
                ? 'Aucun planning'
                : `Planning · ${selectedCount}`;

        this.emptyTarget.hidden = this.eventTargets.some((event) => !event.hidden);
    }

    closeDropdown() {
        this.dropdownTarget.hidden = true;
        this.triggerTarget.setAttribute('aria-expanded', 'false');
    }
}
