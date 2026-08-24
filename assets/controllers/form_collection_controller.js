import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://symfony.com/bundles/StimulusBundle/current/index.html#lazy-stimulus-controllers
*/

/* stimulusFetch: 'lazy' */
export default class extends Controller {


    static targets = [
        'collectionContainer'

    ];


    static values = {
        index    : Number,
        prototype: String,
    };

    addCollectionElement(event)
    {
        const template = document.createElement('template');
        template.innerHTML = this.prototypeValue.replace(/__name__/g, this.indexValue).trim();
        const item = template.content.firstElementChild;
        if (!item) return;
        this.collectionContainerTarget.appendChild(item);
        this.indexValue++;
    }

    removeCollectionElement(event) {
        event.currentTarget.closest('[data-form-collection-item]')?.remove();
    }


}
