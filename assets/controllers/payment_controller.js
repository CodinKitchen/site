import { Controller } from '@hotwired/stimulus';
import {loadStripe} from '@stripe/stripe-js';

export default class extends Controller {
    static targets = ['stripeForm', 'button', 'message', 'spinner'];

    async connect() {
        this.element.addEventListener("submit", this.handleSubmit.bind(this));

        this.stripe = await loadStripe(process.env.STRIPE_PUBLIC_KEY);

        this.elements = this.stripe.elements({clientSecret: this.element.dataset.clientSecret});

        const paymentElement = this.elements.create("payment");
        paymentElement.mount(this.stripeFormTarget);
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.setLoading(true);
      
        const elements = this.elements;
        const { error } = await this.stripe.confirmPayment({
          elements,
          confirmParams: {
            return_url: this.element.dataset.returnUrl,
          },
        });
      
        if (error.type === "card_error" || error.type === "validation_error") {
            this.showMessage(error.message);
        } else {
            this.showMessage("An unexpected error occured.");
        }
      
        this.setLoading(false);
    }

    setLoading(isLoading) {
        if (isLoading) {
            this.buttonTarget.disabled = true;
            this.spinnerTarget.classList.remove('invisible');
        } else {
            this.buttonTarget.disabled = false;
            this.spinnerTarget.classList.add('invisible');
        }
    }

    showMessage(messageText) {
        const messageContainer = this.messageTarget;
      
        messageContainer.classList.remove("hidden");
        messageContainer.textContent = messageText;
      
        setTimeout(function () {
          messageContainer.classList.add("hidden");
          messageText.textContent = "";
        }, 4000);
    }
}
