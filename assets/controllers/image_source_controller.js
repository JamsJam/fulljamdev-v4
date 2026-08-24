import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['fileField', 'urlField', 'previewContainer', 'preview'];
    static values = { currentMedia: String };

    connect() {
        this.refresh();
        this.element.querySelector('input[type="file"]')?.addEventListener('change', (event) => this.previewFile(event));
        this.element.querySelector('input[type="url"]')?.addEventListener('input', () => this.previewUrl());
    }

    change() {
        this.refresh();
    }

    refresh() {
        const source = this.element.querySelector('input[type="radio"]:checked')?.value;
        const usesFile = source === 'media';
        this.fileFieldTarget.hidden = !usesFile;
        this.urlFieldTarget.hidden = usesFile;
        usesFile ? this.previewStoredMedia() : this.previewUrl();
    }

    previewFile(event) {
        const [file] = event.currentTarget.files;
        if (file) this.showPreview(URL.createObjectURL(file));
    }

    previewUrl() {
        const url = this.element.querySelector('input[type="url"]')?.value.trim();
        url ? this.showPreview(url) : this.hidePreview();
    }

    previewStoredMedia() {
        this.currentMediaValue
            ? this.showPreview(`/uploads/pages/${encodeURIComponent(this.currentMediaValue)}`)
            : this.hidePreview();
    }

    showPreview(source) {
        this.previewTarget.src = source;
        this.previewContainerTarget.hidden = false;
    }

    hidePreview() {
        this.previewTarget.removeAttribute('src');
        this.previewContainerTarget.hidden = true;
    }
}
