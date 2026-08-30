import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['search', 'status', 'row', 'empty', 'resultCount'];

    filter() {
        const query = this.searchTarget.value.trim().toLowerCase();
        const status = this.statusTarget.value;
        let visible = 0;

        this.rowTargets.forEach((row) => {
            const matchesSearch = row.dataset.search.includes(query);
            const matchesStatus = status === 'all'
                || row.dataset.status === status
                || (status === 'featured' && row.dataset.featured === 'true');
            row.hidden = !(matchesSearch && matchesStatus);
            if (!row.hidden) visible += 1;
        });

        if (this.hasEmptyTarget) this.emptyTarget.hidden = visible !== 0;
        if (this.hasResultCountTarget) this.resultCountTarget.textContent = `${visible} projet${visible > 1 ? 's' : ''}`;
    }
}
