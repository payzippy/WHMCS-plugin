<?php

require_once(dirname(__FILE__) . '/payzippy-sdk/ChargingRequest.php');
require_once(dirname(__FILE__) . '/payzippy-sdk/Config.php');

function payzippy_config() {
    $configarray = array(
        "FriendlyName" => array("Type" => "System", "Value" => "PayZippy"),
        "merchantid" => array("FriendlyName" => "Merchant ID", "Type" => "text", "Size" => "50", "Description" => "Enter your PayZippy Merchant Id here ..",),
        "merchantkeyid" => array("FriendlyName" => "Merchant Key ID", "Type" => "text", "Size" => "50", "Description" => "Enter your PayZippy Merchant Key Id here ..",),
        "secretkey" => array("FriendlyName" => "Secret Key", "Type" => "text", "Size" => "50", "Description" => "Enter your PayZippy Secret Key here ..",),
    );
    return $configarray;
}

function payzippy_link($params) {
    //$my_config = new Config();

    # Gateway Specific Variables
    $amount = $params['amount']; # Format: ##.##
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];

    $wh_charging = new ChargingRequest($params['secretkey']);
    $wh_charging->set_merchant_id($params['merchantid'])
            ->set_merchant_key_id($params['merchantkeyid'])
            ->set_merchant_transaction_id($params['invoiceid'])
            ->set_currency($params['currency']) # Currency Code
            ->set_transaction_amount($amount * 100)
            ->set_billing_name($firstname . " " . $lastname)
            ->set_buyer_email_address($params['clientdetails']['email'])
            ->set_shipping_address($address1 . $address2)
            ->set_shipping_city($params['clientdetails']['city'])
            ->set_shipping_state($params['clientdetails']['state'])
            ->set_shipping_zip($params['clientdetails']['postcode'])
            ->set_shipping_country($params['clientdetails']['country'])
            ->set_buyer_phone_no($params['clientdetails']['phonenumber'])
            ->set_ui_mode(Config::UI_MODE)
            ->set_transaction_type(Config::TRANSACTION_TYPE)
            ->set_hash_method(Config::HASH_METHOD)
            ->set_source(Config::SOURCE)
            ->set_callback_url($params['systemurl'] . '/modules/gateways/callback/payzippy.php')
            ->set_udf1($params['systemurl'])
            ->set_payment_method(Config::PAYMENT_METHOD);
    $wh_charging->charge();
    $request_params = $wh_charging->get_params();
#add source and other details ...
    # System Variables
    $companyname = $params['companyname'];
    $systemurl = $params['systemurl'];
    $currency = $params['currency'];

    # Enter your code submit to the gateway...

    $code = '<form method = "post" action = "https://www.payzippy.com/payment/api/charging/v1" >
<input type="hidden" name="buyer_email_address" value="' . $request_params['buyer_email_address'] . '">
<input type="hidden" name="billing_name" value="' . $request_params['billing_name'] . '" />
<input type="hidden" name="shipping_address" value="' . $request_params['shipping_address'] . '" />
<input type="hidden" name="shipping_city" value="' . $request_params['shipping_city'] . '" />
<input type="hidden" name="shipping_state" value="' . $request_params['shipping_state'] . '" />
<input type="hidden" name="shipping_zip" value="' . $request_params['shipping_zip'] . '" />
<input type="hidden" name="shipping_country" value="' . $request_params['shipping_country'] . '" />
<input type="hidden" name="buyer_phone_no" value="' . $request_params['buyer_phone_no'] . '" />
<input type="hidden" name="merchant_id"       value="' . $request_params['merchant_id'] . '">
<input type="hidden" name="merchant_key_id"       value="' . $request_params['merchant_key_id'] . '">
<input type="hidden" name="transaction_amount"                value="' . $request_params['transaction_amount'] . '">
<input type="hidden" name="merchant_transaction_id"               value="' . $request_params['merchant_transaction_id'] . '">
<input type="hidden" name="hash"               value="' . $request_params['hash'] . '">
<input type="hidden" name="transaction_type"              value="' . $request_params['transaction_type'] . '">
<input type="hidden" name="ui_mode"               value="' . $request_params['ui_mode'] . '">
<input type="hidden" name="source"               value="' . $request_params['source'] . '">
<input type="hidden" name="payment_method"               value="' . $request_params['payment_method'] . '">
<input type="hidden" name="currency"              value="' . $request_params['currency'] . '">
<input type="hidden" name="hash_method"               value="' . $request_params['hash_method'] . '"> 
<input type="hidden" name="udf1"               value="' . $request_params['udf1'] . '"> 
<input type="hidden" name="callback_url" value="' . $params['systemurl'] . '/modules/gateways/callback/payzippy.php">
<input type="submit" value="Proceed to PayZippy" />
</form>';


    return $code;
}
?>
