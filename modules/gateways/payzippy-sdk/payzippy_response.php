<?php

# Required File Includes
include("../dbconnect.php");
include("../includes/functions.php");
include("../includes/gatewayfunctions.php");
include("../includes/invoicefunctions.php");

$gatewaymodule = "payzippy"; # Enter your gateway module name here replacing template

$GATEWAY = getGatewayVariables($gatewaymodule);
if (!$GATEWAY["type"]) die("Module Not Activated"); # Checks gateway module is active before accepting callback

# Get Returned Variables - Adjust for Post Variable Names from your Gateway's Documentation
$status = $_POST["transaction_response_code"];
$invoiceid = $_POST["merchant_transaction_id"];
$transid = $_POST["payzippy_transaction_id"];
$amount = ($_POST["transaction_amount"])/100;
# $fee = $_POST["x_fee"];

$invoiceid = checkCbInvoiceID($invoiceid,$GATEWAY["name"]); # Checks invoice ID is a valid invoice number or ends processing

checkCbTransID($transid); # Checks transaction number isn't already in the database and ends processing if it does

if ($status=="SUCCESS") {
    # Successful
    addInvoicePayment($invoiceid,$transid,$amount,$fee,$gatewaymodule); # Apply Payment to Invoice: invoiceid, transactionid, amount paid, fees, modulename
	logTransaction($GATEWAY["name"],$_POST,"Successful"); # Save to Gateway Log: name, data array, status
} else {
	# Unsuccessful
    logTransaction($GATEWAY["name"],$_POST,"Unsuccessful"); # Save to Gateway Log: name, data array, status
}

?>