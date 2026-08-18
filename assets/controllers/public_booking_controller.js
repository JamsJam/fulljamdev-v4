import { Controller } from '@hotwired/stimulus';
import { renderStreamMessage } from '@hotwired/turbo';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['form', 'step'];
 
    selectDate() {
        this.submitStep('time');
    }

    selectTime() {
        this.submitStep('contact');
    }

    async selectTimezone(event) {
        const url = new URL(event.currentTarget.dataset.timesUrl, window.location.origin);
        url.searchParams.set('timezone', event.currentTarget.value);

        const response = await fetch(url, {
            headers: { Accept: 'text/vnd.turbo-stream.html' },
            credentials: 'same-origin',
        });

        if (response.ok) {
            renderStreamMessage(await response.text());
        }
    }

    editDate() {
        this.clearRadioGroup('[date][value]');
        this.clearRadioGroup('[time][value]');
        this.submitStep('date');
    }

    editTime() {
        this.clearRadioGroup('[time][value]');
        this.submitStep('time');
    }

    submitBooking() {
        this.stepTarget.value = 'submit';
    }

    submitStep(step) {
        this.stepTarget.value = step;
        this.formTarget.requestSubmit();
    }

    clearRadioGroup(fieldSuffix) {
        this.formTarget.querySelectorAll('input[type="radio"]').forEach((radio) => {
            if (radio.name.endsWith(fieldSuffix)) {
                radio.checked = false;
            }
        });
    }

}
