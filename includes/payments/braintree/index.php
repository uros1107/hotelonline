<?php
require_once('../../../common/lib.php');
require_once('../../../common/define.php');
require_once('init.php');

$clientToken = Braintree_ClientToken::generate();

//~ $gateway = new Braintree_Gateway([
    //~ 'environment' => (PAYMENT_TEST_MODE == 1 ? 'sandbox' : 'production'),
    //~ 'merchantId' => BRAINTREE_MERCHANT_ID,
    //~ 'publicKey' => BRAINTREE_PUBLIC_KEY,
    //~ 'privateKey' => BRAINTREE_PRIVATE_KEY
//~ ]);
//~ 
//~ $result = $gateway->customer()->create([
    //~ 'firstName' => 'Mike',
    //~ 'lastName' => 'Jones',
    //~ 'company' => 'Jones Co.',
    //~ 'paymentMethodNonce' => nonceFromTheClient
//~ ]);
//~ 
//~ if ($result->success) {
    //~ echo($result->customer->id);
    //~ $method_token = $result->customer->paymentMethods[0]->token;
    //~ 
	//~ $result = $gateway->paymentMethodNonce()->create($method_token);
	//~ $nonce = $result->paymentMethodNonce->nonce;
	//~ 
	//~ echo $nonce;
//~ } else {
    //~ foreach($result->errors->deepAll() AS $error) {
        //~ echo($error->code . ": " . $error->message . "\n");
    //~ }
//~ }


?>
<html>
<body>
	
<form action="checkout.php" method="post">
	<div id="dropin"></div>
	<input type="hidden" name="amount" value="10">
	<input type="submit" value="Pay">
</form>
	
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script> 
<script src="https://js.braintreegateway.com/v2/braintree.js"></script> 
<!-- TO DO : Place below JS code in js file and include that JS file --> 
<script type="text/javascript">
	(function() {
		
		var BTFn = {};

		BTFn.sendJSON = function($pay_btn) {

			$.ajax({
				dataType: "text",
				type: "POST",
				data:  { action: "generateclienttoken"},
				url: "checkout.php",
				success: function (req) {
					BTFn.initBT(req, $pay_btn);
				},
				error: function() {
				}
			});
		};

		BTFn.initBT = function(req, $pay_btn) {

			braintree.setup(
				req,
				'dropin', {
					container: 'dropin',
					onReady:function(){
						$('.loader_container').remove();
					},
					onError: function(error) {
						$pay_btn.show().closest('.btn_container').find('.loader_img').hide();
					}
			});
		};

		BTFn.formValidate = function($form, $submit, $amount, $pay_btn) {

			var THIS = this;

			$submit.on('click', function(e) {

				$('.input-label .invalid-bottom-bar').removeClass('invalid');
				$(this).hide().closest('.btn_container').find('.loader_img').css('display', 'inline-block');
			});
		};

		BTFn.updateForm = function($form, link) {
			
			$form.attr('action', link);
			$('.one_off_amount, .monthly_amount').toggleClass('hide');
		};

		BTFn.appendTo = function($cont, childSelector, options) {

			var input = document.createElement(childSelector);
			input.type = options.type;
			input.name = options.name;
			input.value = options.value;
			$cont.appendChild(input);
		};

		$(document).ready(function() {

			$('.loader_container').find("div").show();

			var $form = $('#checkout'), $submit = $('#checkout input[type="submit"]'), $amount = $('input[name="amount"]'), $pay_btn = $('.pay-btn');

			BTFn.sendJSON($pay_btn);
			BTFn.formValidate($form, $submit, $amount, $pay_btn);
		});
	})();

</script>
</body>
</html>
