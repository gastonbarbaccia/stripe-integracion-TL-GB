<?php

use Dotenv\Parser\Value;
use Stripe\PaymentIntent;

require_once 'shared.php';

$precio = $_POST['precio'];


try {
  if($precio == 50000){
  $paymentIntent = $stripe->paymentIntents->create([
    'payment_method_types' => ['oxxo'],
    'amount' => 50000,
    'currency' => 'mxn',
  ]);
  }
  else{
    $paymentIntent = $stripe->paymentIntents->create([
      'payment_method_types' => ['oxxo'],
      'amount' => 100000,
      'currency' => 'mxn',
    ]);
  }
} catch (\Stripe\Exception\ApiErrorException $e) {
  http_response_code(400);
  error_log($e->getError()->message);
?>
  <h1>Error</h1>
  <p>Failed to create a PaymentIntent</p>
  <p>Please check the server logs for more information</p>
<?php
  exit;
} catch (Exception $e) {
  error_log($e);
  http_response_code(500);
  exit;
}
?>


<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>OXXO</title>
    <link rel="stylesheet" href="css/base.css" />

  </head>
  <body>
    <main>
      <!--<a href="/">home</a>-->
      <br>
      <h1>OXXO</h1>

      <form id="payment-form">
        <label for="name">
          Nombre
        </label>
        <input id="name" placeholder="Jenny Rosen" required>

        <label for="email">
          Email
        </label>
        <input id="email" placeholder="jr.succeed_immediately@example.com" required>

        <!-- Used to display form errors. -->
        <div id="error-message" role="alert"></div>

        <button type="submit">Pagar</button>
      </form>

      <div id="messages" role="alert" style="display: none;"></div>
    </main>

    <script src="https://js.stripe.com/v3/"></script>
    <script src="./utils.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', async () => {
        const stripe = Stripe('<?= $_ENV["STRIPE_PUBLISHABLE_KEY"]; ?>', {
          apiVersion: '2020-08-27',
        });
        const paymentForm = document.querySelector('#payment-form');
        paymentForm.addEventListener('submit', async (e) => {
          // Avoid a full page POST request.
          e.preventDefault();

          // Customer inputs
          const nameInput = document.querySelector('#name');
          const emailInput = document.querySelector('#email');

          // Confirm the payment that was created server side:
          const {error, paymentIntent} = await stripe.confirmOxxoPayment(
            '<?= $paymentIntent->client_secret; ?>', {
              payment_method: {
                billing_details: {
                  name: nameInput.value,
                  email: emailInput.value,
                },
              },
            }
          );
          if(error) {
            addMessage(error.message);
            return;
          }
          //addMessage(`Payment (${paymentIntent.id}): ${paymentIntent.status}`); 
          window.location.replace("../../index.php");
        });
      });
    </script>
  </body>
</html>
