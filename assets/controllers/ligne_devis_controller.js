import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
    static targets = ['list', 'addButton'];

    connect() {
        this.index = this.listTarget.children.length;
    }

    add(event) {
        event.preventDefault();
        const prototype = this.listTarget.dataset.prototype.replace(/__name__/g, this.index);
        const removeButton = this.listTarget.dataset.removeButton;

        const newItem = document.createElement('div');
        newItem.classList.add('ligne-devis-item');
        newItem.innerHTML = prototype + removeButton;

        this.listTarget.appendChild(newItem);
        this.index++;
    }

    remove(event) {
        event.preventDefault();
        event.target.closest('.ligne-devis-item').remove();
    }
}