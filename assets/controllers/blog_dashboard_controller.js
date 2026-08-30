import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['row', 'search', 'category', 'empty', 'resultCount', 'drawer'];
    currentStatus = 'all';
    sortKey = null;
    sortDirection = 'ascending';

    selectStatus(event) {
        this.currentStatus = event.currentTarget.dataset.status;
        this.element.querySelectorAll('[role="tab"]').forEach((tab) => {
            const active = tab === event.currentTarget;
            tab.classList.toggle('is-active', active);
            tab.setAttribute('aria-selected', active ? 'true' : 'false');
        });
        this.filter();
    }

    filter() {
        const query = this.normalize(this.searchTarget.value);
        const category = this.normalize(this.categoryTarget.value);
        let visible = 0;
        this.rowTargets.forEach((row) => {
            const show = (this.currentStatus === 'all' || row.dataset.status === this.currentStatus)
                && (!category || this.normalize(row.dataset.category) === category)
                && (!query || this.normalize(row.dataset.search).includes(query));
            row.hidden = !show;
            if (show) visible += 1;
        });
        this.emptyTarget.hidden = visible !== 0;
        this.resultCountTarget.textContent = `${visible} article${visible > 1 ? 's' : ''}`;
    }

    sort(event) {
        const key = event.currentTarget.dataset.sortKey;
        this.sortDirection = this.sortKey === key && this.sortDirection === 'ascending' ? 'descending' : 'ascending';
        this.sortKey = key;
        const multiplier = this.sortDirection === 'ascending' ? 1 : -1;
        const rows = [...this.rowTargets].sort((first, second) => {
            return (Number(first.dataset[`sort${this.capitalize(key)}`]) - Number(second.dataset[`sort${this.capitalize(key)}`])) * multiplier;
        });
        const body = this.rowTargets[0]?.parentElement;
        rows.forEach((row) => body?.append(row));
        this.element.querySelectorAll('.blog-dashboard__sort').forEach((button) => {
            const active = button === event.currentTarget;
            button.classList.toggle('is-active', active);
            button.closest('th')?.setAttribute('aria-sort', active ? this.sortDirection : 'none');
        });
    }

    openCategories() { this.drawerTarget.showModal(); }
    closeCategories() { this.drawerTarget.close(); }
    closeOnBackdrop(event) { if (event.target === this.drawerTarget) this.closeCategories(); }
    normalize(value = '') { return value.toLocaleLowerCase('fr').normalize('NFD').replace(/[\u0300-\u036f]/g, '').trim(); }
    capitalize(value) { return value.charAt(0).toUpperCase() + value.slice(1); }
}
