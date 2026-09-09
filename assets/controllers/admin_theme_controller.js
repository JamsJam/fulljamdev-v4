import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    connect() {
        const savedTheme = window.localStorage.getItem('admin-theme');

        if (savedTheme === 'light' || savedTheme === 'dark') {
            document.documentElement.dataset.theme = savedTheme;
        }
    }

    toggle() {
        const currentTheme = document.documentElement.dataset.theme || 'light';
        const nextTheme = currentTheme === 'light' ? 'dark' : 'light';

        document.documentElement.dataset.theme = nextTheme;
        window.localStorage.setItem('admin-theme', nextTheme);
    }
}
