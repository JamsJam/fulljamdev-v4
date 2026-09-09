import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = [
        'empty',
        'row',
        'rowCheckbox',
        'search',
        'selectAll',
        'selectedCount',
        'status',
        'visibleCount',
    ];

    connect() {
        this.filter();
    }

    filter() {
        const query = this.searchTarget.value.trim().toLocaleLowerCase('fr');
        const status = this.statusTarget.value;

        this.rowTargets.forEach((row) => {
            const matchesQuery = row.dataset.title.includes(query);
            const matchesStatus = 'all' === status || row.dataset.status === status;
            row.hidden = !matchesQuery || !matchesStatus;
        });

        this.updateState();
    }

    toggleAll() {
        this.visibleCheckboxes.forEach((checkbox) => {
            checkbox.checked = this.selectAllTarget.checked;
        });
        this.updateState();
    }

    updateSelection() {
        this.updateState();
    }

    updateState() {
        const visibleRows = this.rowTargets.filter((row) => !row.hidden);
        const visibleCheckboxes = this.visibleCheckboxes;
        const selectedCount = visibleCheckboxes.filter((checkbox) => checkbox.checked).length;

        this.emptyTarget.hidden = visibleRows.length > 0;
        this.visibleCountTarget.textContent = String(visibleRows.length);
        this.selectedCountTarget.textContent = String(selectedCount);
        this.selectAllTarget.checked = visibleCheckboxes.length > 0 && selectedCount === visibleCheckboxes.length;
        this.selectAllTarget.indeterminate = selectedCount > 0 && selectedCount < visibleCheckboxes.length;
    }

    get visibleCheckboxes() {
        return this.rowCheckboxTargets.filter((checkbox) => !checkbox.closest('tr').hidden);
    }
}
