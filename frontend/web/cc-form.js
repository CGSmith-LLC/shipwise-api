
var style = {
    base: {
        color: '#32325d',
        fontFamily: '\"Helvetica Neue\", Helvetica, sans-serif',
        fontSmoothing: 'antialiased',
        fontSize: '16px',
        '::placeholder': {
            color: '#aab7c4'
        }
    },
    invalid: {
        color: '#fa755a',
        iconColor: '#fa755a'
    }
};


var elements = stripe.elements();
var cardElement = elements.create('card', {style: style});
var cardButton = document.getElementById('card-button');
var clientSecret = cardButton.dataset.secret;
cardElement.mount('#card-element');

var cardholderName = document.getElementById('cardholder-name');

// Submit the form with the token ID.
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = $("#add-cc-form");
    var hiddenInput = $("#paymentmethod-stripe_payment_method_id");
    hiddenInput.val(token.payment_method);

    // Submit the form
    form.submit();
}
//display error(s)
cardElement.addEventListener('change',function(event){
    var displayError = $("#card-errors");
    if (event.error) {
        displayError.html(event.error.message);
    } else {
        displayError.html('');
    }
});

function createCC() {
    stripe.confirmCardSetup(
        clientSecret,
        {
            payment_method: {
                card: cardElement,
                billing_details: {
                    name: cardholderName.value,
                },
            },
        },
    ).then(function (result) {
        if (result.error) {
            console.log(result.error);
            $("#card-errors").html(result.error.message);
            //displayError.html(result.error.message);
        } else {
            stripeTokenHandler(result.setupIntent);
            // The setup has succeeded. Display a success message.
        }
    });
}
