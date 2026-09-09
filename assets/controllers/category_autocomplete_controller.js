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

        const response = await fetch(this.urlValue, { headers: { Accept: 'application/json' } });
        if (!response.ok) {
            return;
        }

        const names = await response.json();
        names.forEach((name) => {
            const option = document.createElement('option');
            option.value = name;
            this.list.append(option);
        });

        this.element.setAttribute('list', this.listId);
        this.element.after(this.list);
    }

    disconnect() {
        this.element.removeAttribute('list');
        this.list?.remove();
    }
}
