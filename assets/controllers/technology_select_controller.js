import { Controller } from '@hotwired/stimulus';
import TomSelect from 'tom-select';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static values = {
        placeholder: String,
        url: String,
    };

    connect() {
        this.select = new TomSelect(this.element, {
            plugins: ['remove_button'],
            placeholder: this.placeholderValue || 'Rechercher…',
            create: true,
            createOnBlur: true,
            persist: false,
            delimiter: ',',
            valueField: 'value',
            labelField: 'text',
            searchField: ['text'],
            hideSelected: true,
            closeAfterSelect: true,
        });
        this.loadOptions();
    }

    disconnect() {
        this.select?.destroy();
    }

    async loadOptions() {
        if (!this.hasUrlValue) return;

        const response = await fetch(this.urlValue, { headers: { Accept: 'application/json' } });
        if (!response.ok) return;

        const names = await response.json();
        this.select.addOptions(names.map((name) => ({ value: name, text: name })));
        this.select.refreshOptions(false);
    }
}
