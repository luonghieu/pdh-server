<!DOCTYPE html>
<html>
<head>
  <!-- link to the SqPaymentForm library -->
  <script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>

  <!-- link to the local SqPaymentForm initialization -->
  <script type="text/javascript" src="/assets/web/js/card_square.js">
  </script>

  <!-- link to the custom styles for SqPaymentForm -->
  <link rel="stylesheet" type="text/css" href="/assets/web/css/card_square.css">
</head>

<body>
  <div id="orderSummary">
    <button id='show-paymentform' onclick='buildForm()'>Pay now</button>
    <!--Square template form-container div-->
    <div id="form-container">
      <div id="sq-walletbox">
        <!-- Placeholder for Apple Pay for Web button -->
        <button id="sq-apple-pay"></button>
        <!-- Placeholder for Masterpass button -->
        <button id="sq-masterpass"></button>
        <!-- Placeholder for Google Pay button-->
        <button id="sq-google-pay" class="button-google-pay"></button>
        <div id="sq-walletbox-divider">
          <span id="sq-walletbox-divider-label">Or</span>
          <hr />
        </div>
      </div>

      <div id="sq-ccbox">
        <!--
          Be sure to replace the action attribute of the form with the path of
          the Transaction API charge endpoint URL you want to POST the nonce to
          (for example, "/process-card")
        -->
        <form id="nonce-form" novalidate action="#" method="post">
          {{ csrf_field() }}
          <fieldset>
            <span class="label">Card Number</span>
            <div id="sq-card-number"></div>

            <div class="third">
              <span class="label">Expiration</span>
              <div id="sq-expiration-date"></div>
            </div>

            <div class="third">
              <span class="label">CVV</span>
              <div id="sq-cvv"></div>
            </div>

          </fieldset>

          <button id="sq-creditcard" class="button-credit-card" onclick="requestCardNonce(event)">Pay $1.00</button>

          <div id="error"></div>

          <!--
            After a nonce is generated it will be assigned to this hidden input field.
          -->
          <input type="hidden" id="card-nonce" name="nonce">
        </form>
      </div> <!-- end #sq-ccbox -->

    </div> <!-- end #form-container -->
    <!--end Square template form-container div-->
  </div>
 <!--end Your payment page declaration-->
</body>
</html>
