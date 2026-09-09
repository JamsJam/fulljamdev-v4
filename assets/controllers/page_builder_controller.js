import { Controller } from '@hotwired/stimulus';

/* stimulusFetch: 'lazy' */
export default class extends Controller {
    static targets = ['blocks', 'block', 'content', 'library', 'feedback'];
    static values = { index: Number, fragmentUrl: String };

    connect() {
        this.dragged = null;
        this.blockTargets.forEach((block) => this.prepareDrag(block));
        this.syncPositions();
    }

    toggleLibrary() {
        this.libraryTarget.hidden = !this.libraryTarget.hidden;
    }

    async addBlock(event) {
        event.preventDefault();
        const type = event.currentTarget.dataset.type;
        const label = event.currentTarget.textContent.trim();
        const url = this.fragmentUrlValue
            .replace('block-type-placeholder', encodeURIComponent(type))
            .replace('999999', String(this.indexValue));
        event.currentTarget.disabled = true;
        event.currentTarget.setAttribute('aria-busy', 'true');
        if (this.hasFeedbackTarget) this.feedbackTarget.textContent = '';

        try {
            const response = await fetch(url, { headers: { Accept: 'text/html' } });
            if (!response.ok) throw new Error(`HTTP ${response.status}`);

            const template = document.createElement('template');
            template.innerHTML = (await response.text()).trim();
            const block = template.content.querySelector('[data-page-builder-target~="block"]');
            if (!block) throw new Error('Le fragment ne contient aucun bloc');

            this.blocksTarget.appendChild(block);
            this.prepareDrag(block);
            this.syncPositions();
            this.indexValue++;
            this.libraryTarget.hidden = true;
            if (this.hasFeedbackTarget) this.feedbackTarget.textContent = `${label} a été ajouté.`;
        } catch (error) {
            console.error('Impossible d’ajouter le bloc', error);
            if (this.hasFeedbackTarget) this.feedbackTarget.textContent = 'Le bloc n’a pas pu être ajouté. Réessayez.';
        } finally {
            event.currentTarget.disabled = false;
            event.currentTarget.removeAttribute('aria-busy');
        }
    }

    removeBlock(event) {
        event.currentTarget.closest('[data-page-builder-target="block"]')?.remove();
        this.syncPositions();
    }

    toggleBlock(event) {
        const block = event.currentTarget.closest('[data-page-builder-target="block"]');
        const content = block?.querySelector('[data-page-builder-target="content"]');
        if (!content) return;
        content.hidden = !content.hidden;
        const label = event.currentTarget.querySelector('.button__label');
        if (label) label.textContent = content.hidden ? '+' : '−';
    }

    prepareDrag(block) {
        if (!block || block.dataset.dragReady) return;
        block.dataset.dragReady = 'true';
        const handle = block.querySelector('[data-page-builder-drag-handle]');
        if (!handle) return;
        handle.addEventListener('dragstart', (event) => {
            this.dragged = block;
            block.classList.add('is-dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', 'page-block');
        });
        handle.addEventListener('dragend', () => {
            block.classList.remove('is-dragging');
            this.dragged = null;
            this.syncPositions();
        });
        block.addEventListener('dragover', (event) => {
            event.preventDefault();
            if (!this.dragged || this.dragged === block) return;
            const after = event.clientY > block.getBoundingClientRect().top + block.offsetHeight / 2;
            block.parentNode.insertBefore(this.dragged, after ? block.nextSibling : block);
        });
    }

    syncPositions() {
        this.blockTargets.forEach((block, position) => {
            const input = block.querySelector('[data-page-builder-position]');
            if (input) input.value = String(position);
        });
    }
}
