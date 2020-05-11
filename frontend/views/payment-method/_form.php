<?php


/* @var $this yii\web\View */

\Stripe\Stripe::setApiKey(Yii::$app->stripe->privateKey);

$intent = \Stripe\SetupIntent::create([
        'customer' => Yii::$app->user->identity->getCustomerStripeId(),
]);

$this->registerJsFile('https://js.stripe.com/v3/');
$this->registerJs("var stripe = Stripe('".Yii::$app->stripe->publicKey."');

var elements = stripe.elements();
var cardElement = elements.create('card');
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
        alert(result.error);
      // Display error.message in your UI.
    } else {
        alert(result);
      // The setup has succeeded. Display a success message.
    }
  });
});
");


?>

<div class="payment-method-form">

    <input id="cardholder-name" type="text">
    <!-- placeholder for Elements -->
    <form id="setup-form">
        <div id="card-element"></div>
        <button id="card-button">
            Save Card
        </button>
    </form>

</div>
