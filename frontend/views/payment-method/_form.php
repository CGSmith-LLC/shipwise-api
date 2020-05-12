<?php


/* @var $this yii\web\View */

\Stripe\Stripe::setApiKey(Yii::$app->stripe->privateKey);

$intent = \Stripe\SetupIntent::create([
        'customer' => Yii::$app->user->identity->getCustomerStripeId(),
]);

$this->registerJsFile('https://js.stripe.com/v3/');
$this->registerJs("var stripe = Stripe('".Yii::$app->stripe->publicKey."');

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
cardElement.mount('#card-element');

var cardholderName = document.getElementById('cardholder-name');
var cardButton = document.getElementById('card-button');

cardButton.addEventListener('click', function(ev) {

  stripe.confirmCardSetup(
    '". $intent->client_secret ."',
    {
      payment_method: {
        card: cardElement,
        billing_details: {
          name: cardholderName.value,
        },
      },
    }
  ).then(function(result) {
    if (result.error) {
        console.log(result.error);
        $(\"#card-errors\").html(result.error.message);
        //displayError.html(result.error.message);
    } else {
        stripeTokenHandler(result.setupIntent);
      // The setup has succeeded. Display a success message.
    }
  });
});

// Submit the form with the token ID.
function stripeTokenHandler(token) {
    // Insert the token ID into the form so it gets submitted to the server
    var form = $(\"#add-cc-form\");
    var hiddenInput = $(\"#paymentmethods-stripe_token\");
    hiddenInput.val(token.payment_method);

    // Submit the form
    //form.submit();
}
//display error(s)
cardElement.addEventListener('change',function(event){
    var displayError = $(\"#card-errors\");
    if (event.error) {
        displayError.html(event.error.message);
    } else {
        displayError.html('');
    };
});
");


?>

<div class="payment-method-form">

    <div class="form-row">
        <label for="card-element">
            Credit or Debit Card
        </label>
        <div id="card-element">
            <!-- A Stripe Element will be inserted here. -->
        </div>
        <!-- Used to display form errors. -->
        <div id="card-errors" role="alert"></div>
    </div>


    <input id="cardholder-name" type="text">
    <!-- placeholder for Elements -->
    <form id="add-cc-form"  onsubmit="event.preventDefault();">
        <button id="card-button">
            Save Card
        </button>
    </form>

</div>
