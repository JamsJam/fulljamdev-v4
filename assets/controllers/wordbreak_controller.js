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
    ]
    static values = {
        numBeforeBeak: {type:Number, default: 40},
    }
    initialize(){}

    connect(){}

    descriptionTargetConnected(element){
        element.innerText = element.innerText.replaceAll('<div>','')
        element.innerText = element.innerText.replaceAll('</div>','')
        element.innerText = element.innerText.replaceAll('&nbsp;','')
        
        if(element.innerText.length > this.numBeforeBeakValue){

            element.innerText =   element.innerText.slice(0, this.numBeforeBeakValue) + '...' 
        }
        

        
    }

    descriptionFullTargetConnected(element){
        element.innerText = element.innerText.replaceAll('<div>','')
        element.innerText = element.innerText.replaceAll('</div>','')
        element.innerText = element.innerText.replaceAll('&nbsp;','')
        

        
        
        
    }
}
