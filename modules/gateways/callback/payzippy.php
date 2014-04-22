<?php

# Required File Includes
include("../../../dbconnect.php");
include("../../../includes/functions.php");
include("../../../includes/gatewayfunctions.php");
include("../../../includes/invoicefunctions.php");
require_once(dirname(__FILE__) . '/../payzippy-sdk/Config.php');

$gatewaymodule = "payzippy"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);
$secret_key = $GATEWAY["secretkey"];
if (!$GATEWAY["type"])
    die("Module Not Activated");# Checks gateway module is active before accepting callback

$request_params = $_REQUEST;
unset($_REQUEST);

$hash_received = $request_params['hash'];
$hash_calculated = generate_hash($request_params, $secret_key);

function generate_hash($request_params, $secret_key) {
    $hash_string = '';
    unset($request_params['hash']);
    ksort($request_params);

    foreach ($request_params as $key => $value)
        $hash_string = $hash_string . $value . '|';

    $hash_string = $hash_string . $secret_key;

    //$my_config = new Config();
    return hash(Config::HASH_METHOD, $hash_string);
}

# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
$status = $request_params["transaction_response_code"];
$invoiceid = $request_params["merchant_transaction_id"];
$transid = $request_params["payzippy_transaction_id"];
$amount = ($request_params["transaction_amount"]) / 100;

$invoiceid = checkCbInvoiceID($invoiceid, $GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

if ($status == "SUCCESS" && $hash_calculated == $hash_received) {
    # Successful 
    addInvoicePayment($invoiceid, $transid, $amount, $gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
    logTransaction($GATEWAY["name"], $request_params, "Successful"); # Save to Gateway Log: name, data array, status
} elseif ($status == "SUCCESS" && $hash_calculated != $hash_received) {
    # Hash Mismatch
    logTransaction($GATEWAY["name"], $request_params, "Unsuccessful-Hash Mismatch"); # Save to Gateway Log: name, data array, status
} else {
    #Unsuccessful
    logTransaction($GATEWAY["name"], $request_params, "Unsuccessful-" . $request_params['transaction_response_message']); # Save to Gateway Log: name, data array, status
}

$filename = $request_params['udf1'] . '/viewinvoice.php?id=' . $invoiceid;
HEADER("location:$filename");
?>
