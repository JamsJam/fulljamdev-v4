import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['label'];
    static values = { text: String };

    async copy() {
        try {
            await navigator.clipboard.writeText(this.textValue);
        } catch {
            this.fallbackCopy();
        }

        const originalLabel = this.labelTarget.textContent;
        this.labelTarget.textContent = 'Lien copié !';
        window.setTimeout(() => { this.labelTarget.textContent = originalLabel; }, 2000);
    }

    fallbackCopy() {
        const textarea = document.createElement('textarea');
        textarea.value = this.textValue;
        textarea.setAttribute('readonly', '');
        textarea.style.position = 'fixed';
        textarea.style.opacity = '0';
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        textarea.remove();
    }
}
