import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['fileField', 'urlField', 'previewContainer', 'preview'];
    static values = { currentMedia: String };

    connect() {
        this.refresh();
        this.fileInput = this.element.querySelector('input[type="file"]');
        this.urlInput = this.element.querySelector('input[type="url"]');
        this.previewFile = this.previewFile.bind(this);
        this.previewUrl = this.previewUrl.bind(this);
        this.fileInput?.addEventListener('change', this.previewFile);
        this.urlInput?.addEventListener('input', this.previewUrl);
    }

    disconnect() {
        this.fileInput?.removeEventListener('change', this.previewFile);
        this.urlInput?.removeEventListener('input', this.previewUrl);
        this.revokePreviewUrl();
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
        if (!file) return;

        this.revokePreviewUrl();
        this.previewUrlObject = URL.createObjectURL(file);
        this.showPreview(this.previewUrlObject);
    }

    previewUrl() {
        const url = this.element.querySelector('input[type="url"]')?.value.trim();
        this.revokePreviewUrl();
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

    revokePreviewUrl() {
        if (this.previewUrlObject) {
            URL.revokeObjectURL(this.previewUrlObject);
            this.previewUrlObject = null;
        }
    }
}
