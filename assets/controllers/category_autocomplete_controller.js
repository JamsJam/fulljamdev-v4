import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        url: String,
    };

    async connect() {
        this.listId = `${this.element.id}-suggestions`;
        this.list = document.createElement('datalist');
        this.list.id = this.listId;

        const abortController = new AbortController();
        this.abortController = abortController;
        try {
            const response = await fetch(this.urlValue, {
                headers: { Accept: 'application/json' },
                signal: abortController.signal,
            });
            if (!response.ok) return;

            const names = await response.json();
            if (abortController.signal.aborted || this.abortController !== abortController) return;

            names.forEach((name) => {
                const option = document.createElement('option');
                option.value = name;
                this.list.append(option);
            });

            this.element.setAttribute('list', this.listId);
            this.element.after(this.list);
        } catch (error) {
            if (error.name !== 'AbortError') {
                console.error('Impossible de charger les catégories', error);
            }
        }
    }

    disconnect() {
        this.abortController?.abort();
        this.element.removeAttribute('list');
        this.list?.remove();
    }
}
