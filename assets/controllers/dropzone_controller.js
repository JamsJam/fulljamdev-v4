import { Controller } from '@hotwired/stimulus';


/* stimulusFetch: 'lazy' */
export default class extends Controller {

    static targets = [
        'input',
        'container',
        'previewContainer',
        'placeholder',
        'count',
        'clear',
    ];

    static values = {
        hasCurrent: Boolean,
        currentCount: Number,
    };


    connect() {
        this.initialPreview = this.previewContainerTarget.innerHTML;
    }

    disconnect() {
        // You should always remove listeners when the controller is disconnected to avoid side-effects

    }

    /**
     * 
     * @param {*} event 
     */
    onFileInputChange(event) {

        const filelist = event.target.files;
        this.previewContainerTarget.innerHTML = '';

        this.updateCount(filelist.length);
        for (const file of filelist) {

            if (file.type.startsWith('image/', 0)) {
                const reader = new FileReader();
                reader.addEventListener('load', (fileEvent) => {
                    this.createPreviewItem(file.name, fileEvent.target.result, null);
                });
                reader.readAsDataURL(file);
            } else if (file.type === 'application/pdf') {
                this.createPreviewItem(file.name, null, '📄');
            }
            else if (file.type === 'video/mp4' || file.type === 'video/mp4') {
                this.createPreviewItem(file.name, null, '🎥');
            }
            else if (file.type === 'text/csv') {
                this.createPreviewItem(file.name, null, '📊');
            }
        }
        this.activatePreview();
    }

    onClear(){
        this.inputTarget.value = '';
        this.previewContainerTarget.innerHTML = this.initialPreview;
        this.updateCount(this.currentCountValue);
        this.hasCurrentValue ? this.activatePreview() : this.desactivatePreview();

    }

    /**
     * @description Display how many file are in the file input
     * @param {number} count
     * @returns {void}
     */
    updateCount(count) {
        this.countTarget.innerText = `${count.toString()} fichier${count > 1 ? 's' : ''}`;
    }

    /**
     * @description Create an item_preview for one file
     * @param {string} name 
     * @param {string?} image
     * @param {string?} icon 
     * @returns {HTMLElement}
     */
    createPreviewItem(name, image = null, icon = null) {
        const item = document.createElement('div');
        item.classList.add('dropzone-preview__item');
        const preview = document.createElement('div');
        preview.classList.add('dropzone-preview__image');
        const filename = document.createElement('div');
        filename.classList.add('dropzone-preview__filename');
        filename.textContent = name;

        if (image) {
            preview.style.backgroundImage = `url(${image})`;
        } else {
            preview.textContent = icon;
        }

        item.append(preview, filename);
        this.previewContainerTarget.append(item);
    }

    activatePreview(){
        this.placeholderTarget.classList.add('hide');
        this.previewContainerTarget.classList.remove('hide');
    }

    desactivatePreview(){
        this.placeholderTarget.classList.remove('hide');
        this.previewContainerTarget.classList.add('hide');
    }

    onDragEnter() {
        this.containerTarget.classList.add('draghover');
    }

    onDragLeave() {
        this.containerTarget.classList.remove('draghover');
    }

    onDrop() {
        this.containerTarget.classList.remove('draghover');
    }






}
