<!DOCTYPE html>
<html>
<head>
<title>Cheers</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
<link rel="stylesheet" href="{{ mix('assets/webview/css/style.min.css') }}"/>
  <link href="{{ mix('assets/web/css/web.css') }}" rel="stylesheet">

  <link rel="stylesheet" type="text/css" href="{{ mix('assets/web/css/card_square.min.css') }}">

</head>
<body>
  <div class="border-bottom header-webview">
      <div class="btn-back header-item">
        <a href="cheers://back"><img src="/assets/webview/images/back.png" alt=""></a>
      </div>
      <div class="title-main header-item">
        <span>クレジットカード登録</span>
      </div>
      <div class="btn-register header-item">
        <a id="sq-creditcard" onclick="requestCardNonce(event)" class="color-btn-create">完了</a>
      </div>
  </div>
  <div class="image-main-webview">
    <img src="/assets/webview/images/ic_credit_cards@2x.png" alt="">
  </div>
  <div class="notify-webview">
    <span></span>
  </div>
  <div class="content">
    <div id="orderSummary">
      <div class="sub-title">
        <p>カード情報</p>
      </div>
      <div id="form-container">
        <div id="sq-ccbox">
          <form id="nonce-form" novalidate>
            {{ csrf_field() }}
            <fieldset>
              <div class="card-number border-bottom">
                <span class="left">カード番号</span>
                <div class="right number right-number-square">
                  <div class="wrap-card-number wrap-card-number-webview">
                    <div id="sq-card-number"></div>
                  </div>
                </div>
              </div>
              <div class="clear"></div>
              <div class="expiration-date border-bottom">
                <span class="left title-expiration-date">有効期限</span>
                <div class="date-select right">
                  <div class="wrap-expiration-date wrap-expiration-date-webview">
                    <div id="sq-expiration-date"></div>
                  </div>
                </div>
              </div>
              <div class="sub-title">
                <p>セキュリティコード</p>
              </div>
              <div class="security-code border-bottom">
                <img src="/assets/webview/images/ic_card_cvv.png" alt="" class="left">
                <div class="wrap-cvv">
                  <div id="sq-cvv"></div>
                </div>
              </div>
            </fieldset>

            <div id="error"></div>

            <input type="hidden" id="card-nonce" name="nonce">
          </form>
        </div> <!-- end #sq-ccbox -->

      </div> <!-- end #form-container -->
      <!--end Square template form-container div-->
    </div>
  </div>
  <script src="{{ mix('assets/webview/js/script.min.js') }}"></script>
  <script src="{{ mix('assets/webview/js/create_card.min.js') }}"></script>
  <script src="/assets/webview/js/lib/payment.js"></script>
  <script type="text/javascript" src="https://js.squareup.com/v2/paymentform"></script>
  <script>
      // Set the application ID
      var applicationId = '{!! config('services.square.application_id') !!}';

      // Set the location ID
      var locationId = '{!! config('services.square.location_id') !!}';

      /*
       * function: requestCardNonce
       *
       * requestCardNonce is triggered when the "Pay with credit card" button is
       * clicked
       *
       * Modifying this function is not required, but can be customized if you
       * wish to take additional action when the form button is clicked.
       */

      // Create and initialize a payment form object
      var paymentForm = new SqPaymentForm({
          // Initialize the payment form elements
          applicationId: applicationId,
          locationId: locationId,
          inputClass: 'sq-input',
          autoBuild: false,

          // Customize the CSS for SqPaymentForm iframe elements
          inputStyles: [{
              fontSize: '15px',
              fontFamily: 'Helvetica Neue',
              padding: '10px',
              color: '#373F4A',
              backgroundColor: 'transparent',
              lineHeight: '24px',
              placeholderColor: '#CCC',
              _webkitFontSmoothing: 'antialiased',
              _mozOsxFontSmoothing: 'grayscale',
          }],

          // Initialize the credit card placeholders
          cardNumber: {
              elementId: 'sq-card-number',
              placeholder: '0000 0000 0000 0000'
          },
          cvv: {
              elementId: 'sq-cvv',
              placeholder: 'CVV'
          },
          expirationDate: {
              elementId: 'sq-expiration-date',
              placeholder: 'MM/YY'
          },
          postalCode: false,

          // SqPaymentForm callback functions
          callbacks: {

              /*
               * callback function: methodsSupported
               * Triggered when: the page is loaded.
               */
              methodsSupported: function (methods) {

                  var walletBox = document.getElementById('sq-walletbox');
                  var applePayBtn = document.getElementById('sq-apple-pay');
                  var googlePayBtn = document.getElementById('sq-google-pay');
                  var masterpassBtn = document.getElementById('sq-masterpass');

                  // Only show the button if Apple Pay for Web is enabled
                  // Otherwise, display the wallet not enabled message.
                  if (methods.applePay === true) {
                      walletBox.style.display = 'block';
                      applePayBtn.style.display = 'block';
                  }
                  // Only show the button if Masterpass is enabled
                  // Otherwise, display the wallet not enabled message.
                  if (methods.masterpass === true) {
                      walletBox.style.display = 'block';
                      masterpassBtn.style.display = 'block';
                  }
                  // Only show the button if Google Pay is enabled
                  if (methods.googlePay === true) {
                      walletBox.style.display = 'block';
                      googlePayBtn.style.display = 'inline-block';
                  }
              },

              /*
               * callback function: createPaymentRequest
               * Triggered when: a digital wallet payment button is clicked.
               */
              createPaymentRequest: function () {

                  return {
                      requestShippingAddress: false,
                      requestBillingInfo: false,
                      currencyCode: "JPY",
                      countryCode: "JP"
                  }
              },

              /*
               * callback function: validateShippingContact
               * Triggered when: a shipping address is selected/changed in a digital
               *                 wallet UI that supports address selection.
               */
              validateShippingContact: function (contact) {

                  var validationErrorObj;
                  /* ADD CODE TO SET validationErrorObj IF ERRORS ARE FOUND */
                  return validationErrorObj;
              },

              /*
               * callback function: cardNonceResponseReceived
               * Triggered when: SqPaymentForm completes a card nonce request
               */
              cardNonceResponseReceived: function (errors, nonce, cardData) {
                  if (errors) {
                      // Log errors from nonce generation to the Javascript console
                      console.log("Encountered errors:");
                      errors.forEach(function (error) {
                          console.log('  ' + error.message);
                          alert(error.message);
                      });

                      return;
                  }
                  // Assign the nonce value to the hidden form field
                  document.getElementById('card-nonce').value = nonce;

                  // POST the nonce form to the payment processing page
                  // document.getElementById('nonce-form').submit();
                  submitSquareForm();
              },

              /*
               * callback function: unsupportedBrowserDetected
               * Triggered when: the page loads and an unsupported browser is detected
               */
              unsupportedBrowserDetected: function () {
                  /* PROVIDE FEEDBACK TO SITE VISITORS */
              },

              /*
               * callback function: inputEventReceived
               * Triggered when: visitors interact with SqPaymentForm iframe elements.
               */
              inputEventReceived: function (inputEvent) {
                  switch (inputEvent.eventType) {
                      case 'focusClassAdded':
                          /* HANDLE AS DESIRED */
                          break;
                      case 'focusClassRemoved':
                          /* HANDLE AS DESIRED */
                          break;
                      case 'errorClassAdded':
                          document.getElementById("error").innerHTML = "Please fix card information errors before continuing.";
                          break;
                      case 'errorClassRemoved':
                          /* HANDLE AS DESIRED */
                          document.getElementById("error").style.display = "none";
                          break;
                      case 'cardBrandChanged':
                          /* HANDLE AS DESIRED */
                          break;
                      // case 'postalCodeChanged':
                      /* HANDLE AS DESIRED */
                      // break;
                  }
              },

              /*
               * callback function: paymentFormLoaded
               * Triggered when: SqPaymentForm is fully loaded
               */
              paymentFormLoaded: function () {
                  /* HANDLE AS DESIRED */
                  console.log("The form loaded!");
              }
          }
      });

      function requestCardNonce(event) {
          // Don't submit the form until SqPaymentForm returns with a nonce
          event.preventDefault();

          // Request a nonce from the SqPaymentForm object
          paymentForm.requestCardNonce();
      }

      function buildForm() {
          if (SqPaymentForm.isSupportedBrowser()) {
              var paymentDiv = document.getElementById("form-container");
              if (paymentDiv.style.display === "none") {
                  paymentDiv.style.display = "block";
              }
              paymentform.build();
              paymentform.recalculateSize();
          } else {
              // Show a "Browser is not supported" message to your buyer
          }
      }

      document.addEventListener("DOMContentLoaded", function(event) {
          if (SqPaymentForm.isSupportedBrowser()) {
              paymentForm.build();
              paymentForm.recalculateSize();
          }
      });

      function submitSquareForm() {
          var backUrl = $("#back-url").val();
          var nonce = $("#card-nonce").val();

          $.ajax({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              },
              type: "POST",
              dataType: "json",
              url: '/webview/card/create',
              data: {
                  nonce: nonce,
              },
              success: function (msg) {
                  if (!msg.success) {
                      var error = msg.error;
                      $(".notify span").text(error);
                  } else {
                      window.location.href = 'cheers://adding_card?result=1';
                  }
              },
              error: function(xhr, status, error) {
                  var error = 'このクレジットカードはご利用できません';

                  $(".notify span").text(error);
              }
          });
      }
  </script>
</body>
</html>
