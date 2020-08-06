<?php
define('SITE_TITLE', '');
define('TIME_ZONE', 'Europe/London');
define('DATE_FORMAT', '%F');
define('TIME_FORMAT', '%I:%M %P');
define('CURRENCY_ENABLED', '1');
define('CURRENCY_POS', 'before'); // before or after
define('LANG_ENABLED', '0');
define('ADMIN_LANG_FILE', 'en.ini');
define('ENABLE_COOKIES_NOTICE', '1');
define('MAINTENANCE_MODE', '0');
define('MAINTENANCE_MSG', '<h1><i class=\"fa fa-rocket\"></i> Coming soon...</h1><p>We are sorry, our website is down for maintenance.</p>');
define('TEMPLATE', 'default');
define('OWNER', '');
define('EMAIL', '');
define('ADDRESS', '');
define('PHONE', '');
define('MOBILE', '');
define('FAX', '');
define('DB_NAME', 'your_database_name');
define('DB_HOST', 'localhost');
define('DB_PORT', '3306');
define('DB_USER', 'your_database_user');
define('DB_PASS', 'your_database_password');
define('SENDER_EMAIL', '');
define('SENDER_NAME', '');
define('USE_SMTP', '0');
define('SMTP_SECURITY', '');
define('SMTP_AUTH', '0');
define('SMTP_HOST', '');
define('SMTP_USER', '');
define('SMTP_PASS', '');
define('SMTP_PORT', '25');
define('GMAPS_API_KEY', '');
define('ANALYTICS_CODE', '');
define('ADMIN_FOLDER', 'admin');
define('CAPTCHA_PKEY', ''); // ReCAPTCHA public key
define('CAPTCHA_SKEY', ''); // ReCAPTCHA secret key
define('AUTOGEOLOCATE', '0'); // Change the currency according to the country (https required)
define('PAYMENT_TYPE', 'arrival'); // 2checkout,paypal,check,arrival
define('PAYPAL_EMAIL', '');
define('VENDOR_ID', ''); // 2Checkout.com account number
define('SECRET_WORD', ''); // 2Checkout.com secret word
define('PAYMENT_TEST_MODE', '1');
define('ENABLE_DOWN_PAYMENT', '1');
define('DOWN_PAYMENT_RATE', '30'); // %
define('DOWN_PAYMENT_AMOUNT', '50'); // amount required to activate the down payment
define('ENABLE_TOURIST_TAX', '1');
define('TOURIST_TAX', '0');
define('TOURIST_TAX_TYPE', 'fixed');
define('ALLOW_COMMENTS', '1');
define('ALLOW_RATINGS', '1'); // If comments enabled
define('ENABLE_BOOKING_REQUESTS', '0'); // Possibility to make a reservation request if no availability
define('ENABLE_MULTI_VENDORS', '0'); // Payments are made on the PayPal account of the hotels
define('BRAINTREE_MERCHANT_ID', '');
define('BRAINTREE_PUBLIC_KEY', '');
define('BRAINTREE_PRIVATE_KEY', '');
define('CURRENCY_CONVERTER_KEY', ''); // currencyconverterapi.com API key
define('SHOW_CALENDAR', '0');
define('RAZORPAY_KEY_ID', '');
define('RAZORPAY_KEY_SECRET', '');
define('ENABLE_ICAL', '1');
define('ENABLE_AUTO_ICAL_SYNC', '1');
define('ICAL_SYNC_INTERVAL', 'daily'); // daily | hourly
define('ICAL_SYNC_CLOCK', '3'); // 0-23h mode, required if ICAL_SYNC_INTERVAL = daily
