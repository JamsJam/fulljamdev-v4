import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['manual', 'dynamic'];

    connect() {
        this.toggle();
    }

    toggle() {
        const checked = this.element.querySelector('input[type="radio"]:checked');
        const dynamic = checked?.value === 'dynamic';
        this.manualTarget.hidden = dynamic;
        this.dynamicTarget.hidden = !dynamic;
    }
}
