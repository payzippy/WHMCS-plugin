<?php

final class Config
{
    const TRANSACTION_TYPE = "SALE";
    const CURRENCY = "INR";
    const UI_MODE = "REDIRECT"; // UI Integration - REDIRECT or IFRAME
    const HASH_METHOD = "SHA256"; // MD5 or SHA256

    const API_BASE = "https://www.payzippy.com/payment/api/";
    const API_CHARGING = "charging";
    const API_QUERY = "query";
    const API_REFUND = "refund";
    const API_VERSION = "v1";
    const VERIFY_SSL_CERTS = TRUE;
    const PAYMENT_METHOD = "PAYZIPPY";
    const SOURCE = "WHMCS"; //TODO versions
}
?>
