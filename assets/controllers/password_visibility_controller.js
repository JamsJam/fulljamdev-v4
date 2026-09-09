import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['input', 'button'];

    toggle() {
        const passwordIsVisible = this.inputTarget.type === 'text';

        this.inputTarget.type = passwordIsVisible ? 'password' : 'text';
        this.buttonTarget.setAttribute('aria-label', passwordIsVisible ? 'Afficher le mot de passe' : 'Masquer le mot de passe');
        this.buttonTarget.setAttribute('aria-pressed', String(!passwordIsVisible));
    }
}
