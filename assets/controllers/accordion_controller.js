// assets/controllers/accordion_controller.js
import { Controller } from '@hotwired/stimulus';

export default class extends Controller {
  static targets = ['content', 'trigger'];
  toggle(event) {
    const trigger = event.currentTarget;
    const index = trigger.dataset.index;
    const content = this.contentTargets.find(c => c.dataset.index === index);

    content.classList.toggle('active');
    const icon = trigger.querySelector('span:last-child');
    icon.textContent = content.classList.contains('active') ? '▲' : '▼';
  }
}