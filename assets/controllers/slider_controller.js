import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['input', 'output'];
    static values = {
        min: Number,
        max: Number,
        suffix: String,
    };

    connect() {
        this.update();
    }

    update() {
        const value = Number(this.inputTarget.value);
        const range = this.maxValue - this.minValue;
        const progress = range > 0 ? ((value - this.minValue) / range) * 100 : 0;

        this.outputTarget.textContent = `${value}${this.suffixValue}`;
        this.inputTarget.style.setProperty('--slider-progress', `${progress}%`);
    }
}
