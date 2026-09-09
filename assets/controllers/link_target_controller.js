import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['type', 'route', 'url'];

    connect() {
        this.toggle();
    }

    toggle() {
        const usesRoute = this.typeTarget.value === 'route';
        this.routeTarget.hidden = !usesRoute;
        this.urlTarget.hidden = usesRoute;
    }
}
