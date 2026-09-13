import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['flash'];
    static values = {
        countdown: { type: Number, default: 5000 },
        transition: { type: Number, default: 300 },
    };

    connect() {
        this.fadeTimeout = window.setTimeout(() => {
            this.flashFadeDown();
        }, this.countdownValue);
    }

    flashFadeDown() {
        this.flashTarget.classList.add('hide');

        this.removeTimeout = window.setTimeout(() => {
            this.element.remove();
        }, this.transitionValue);
    }

    disconnect() {
        window.clearTimeout(this.fadeTimeout);
        window.clearTimeout(this.removeTimeout);
    }
}
