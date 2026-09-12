import { Controller } from '@hotwired/stimulus';

/*
* The following line makes this controller "lazy": it won't be downloaded until needed
* See https://github.com/symfony/stimulus-bridge#lazy-controllers
*/
/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = [
        'description',
        'descriptionFull'
    ];
    static values = {
        numBeforeBeak: {type:Number, default: 40},
    };
    initialize(){}

    connect(){}

    descriptionTargetConnected(element){
        element.textContent = this.normalizedText(element.textContent);
        
        if(element.innerText.length > this.numBeforeBeakValue){

            element.textContent = element.textContent.slice(0, this.numBeforeBeakValue) + '...';
        }
        

        
    }

    descriptionFullTargetConnected(element){
        element.textContent = this.normalizedText(element.textContent);
        

        
        
        
    }

    normalizedText(value) {
        return value.replace(/\u00a0/g, ' ');
    }
}
