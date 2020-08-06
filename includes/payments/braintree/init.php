<?php
require_once('lib/Braintree.php');

Braintree_Configuration::environment((PAYMENT_TEST_MODE == 1 ? 'sandbox' : 'production'));
Braintree_Configuration::merchantId(BRAINTREE_MERCHANT_ID);
Braintree_Configuration::publicKey(BRAINTREE_PUBLIC_KEY);
Braintree_Configuration::privateKey(BRAINTREE_PRIVATE_KEY);
