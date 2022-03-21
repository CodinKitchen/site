import {Controller} from '@hotwired/stimulus';
import {loadStripe} from '@stripe/stripe-js';

export default class extends Controller {
    static targets = ['stripeForm', 'button', 'message', 'spinner', 'paymentMethod'];

    async connect() {
        this.element.addEventListener("submit", this.handleSubmit.bind(this));

        this.stripe = await loadStripe(process.env.STRIPE_PUBLIC_KEY);

        const elements = this.stripe.elements();

        const stripeStyle = {
            base: {
                color: "#32325d",
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: "antialiased",
                fontSize: "16px",
                "::placeholder": {
                    color: "#6d7882"
                }
            },
            invalid: {
                color: "#fa755a",
                iconColor: "#fa755a"
            },
        };

        this.paymentElement = elements.create("card", {
            style: stripeStyle
        });
        this.paymentElement.mount(this.stripeFormTarget);
    }

    async handleSubmit(e) {
        e.preventDefault();
        this.setLoading(true);

        const paymentElement = this.paymentElement;
        const result = await this.stripe.createPaymentMethod({
            type: 'card',
            card: paymentElement
        });

        if (result.error) {
            if (result.error.type === "card_error" || error.type === "validation_error") {
                this.showMessage(error.message);
                this.setLoading(false);
            } else {
                this.showMessage("An unexpected error occured.");
                this.setLoading(false);
            }

            return;
        }

        this.paymentMethodTarget.value = result.paymentMethod.id;
        
        this.setLoading(false);
        
        e.target.submit();
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
