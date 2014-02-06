<?php

require_once 'Base.php';

class Paypal extends Base{

    function CallShortcutExpressCheckout($paymentAmount, $currencyCodeType, $paymentType) {

        $nvpstr = "&PAYMENTREQUEST_0_AMT=" . $paymentAmount;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&RETURNURL=" . $this->_return_url;
        $nvpstr = $nvpstr . "&CANCELURL=" . $this->_cancel_url;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
        $_SESSION["currencyCodeType"] = $currencyCodeType;
        $_SESSION["PaymentType"] = $paymentType;

        $resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
            $token = urldecode($resArray["TOKEN"]);
            $_SESSION['TOKEN'] = $token;
        }
    }

    

    function CallMarkExpressCheckout($paymentAmount, $currencyCodeType, $paymentType, $shipToName, $shipToStreet, $shipToCity, $shipToState, $shipToCountryCode, $shipToZip, $shipToStreet2, $phoneNum
    ) {

        $nvpstr = "&PAYMENTREQUEST_0_AMT=" . $paymentAmount;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&RETURNURL=" . $this->_return_url;
        $nvpstr = $nvpstr . "&CANCELURL=" . $this->_cancel_url;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_CURRENCYCODE=" . $currencyCodeType;
        $nvpstr = $nvpstr . "&ADDROVERRIDE=1";
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTONAME=" . $shipToName;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET=" . $shipToStreet;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTREET2=" . $shipToStreet2;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCITY=" . $shipToCity;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOSTATE=" . $shipToState;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE=" . $shipToCountryCode;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOZIP=" . $shipToZip;
        $nvpstr = $nvpstr . "&PAYMENTREQUEST_0_SHIPTOPHONENUM=" . $phoneNum;
        $_SESSION["currencyCodeType"] = $currencyCodeType;
        $_SESSION["PaymentType"] = $paymentType;

        $resArray = $this->hash_call("SetExpressCheckout", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
            $token = urldecode($resArray["TOKEN"]);
            $_SESSION['TOKEN'] = $token;
        }
        return $resArray;
    }


    function GetShippingDetails($token) {

        $nvpstr = "&TOKEN=" . $token;

        $resArray = hash_call("GetExpressCheckoutDetails", $nvpstr);
        $ack = strtoupper($resArray["ACK"]);
        if ($ack == "SUCCESS" || $ack == "SUCCESSWITHWARNING") {
            $_SESSION['payer_id'] = $resArray['PAYERID'];
        }
        return $resArray;
    }

   

    function ConfirmPayment($FinalPaymentAmt) {

        $token = urlencode($_SESSION['TOKEN']);
        $paymentType = urlencode($_SESSION['PaymentType']);
        $currencyCodeType = urlencode($_SESSION['currencyCodeType']);
        $payerID = urlencode($_SESSION['payer_id']);
        $serverName = urlencode($_SERVER['SERVER_NAME']);
        $nvpstr = '&TOKEN=' . $token . '&PAYERID=' . $payerID .
                  '&PAYMENTREQUEST_0_PAYMENTACTION=' .
                   $paymentType . '&PAYMENTREQUEST_0_AMT=' . $FinalPaymentAmt .
                  '&PAYMENTREQUEST_0_CURRENCYCODE=' . $currencyCodeType . '&IPADDRESS=' .
                   $serverName;
        $resArray = $this->hash_call("DoExpressCheckoutPayment", $nvpstr);
      
        //$ack = strtoupper($resArray["ACK"]);
        return $resArray;
    }

   
    function DirectPayment($paymentType, $paymentAmount, $creditCardType, $creditCardNumber, $expDate, $cvv2, $firstName, $lastName, $street, $city, $state, $zip, $countryCode, $currencyCode) {

        $nvpstr = "&AMT=" . $paymentAmount;
        $nvpstr = $nvpstr . "&CURRENCYCODE=" . $currencyCode;
        $nvpstr = $nvpstr . "&PAYMENTACTION=" . $paymentType;
        $nvpstr = $nvpstr . "&CREDITCARDTYPE=" . $creditCardType;
        $nvpstr = $nvpstr . "&ACCT=" . $creditCardNumber;
        $nvpstr = $nvpstr . "&EXPDATE=" . $expDate;
        $nvpstr = $nvpstr . "&CVV2=" . $cvv2;
        $nvpstr = $nvpstr . "&FIRSTNAME=" . $firstName;
        $nvpstr = $nvpstr . "&LASTNAME=" . $lastName;
        $nvpstr = $nvpstr . "&STREET=" . $street;
        $nvpstr = $nvpstr . "&CITY=" . $city;
        $nvpstr = $nvpstr . "&STATE=" . $state;
        $nvpstr = $nvpstr . "&COUNTRYCODE=" . $countryCode;
        $nvpstr = $nvpstr . "&IPADDRESS=" . $_SERVER['REMOTE_ADDR'];
        $resArray = $this->hash_call("DoDirectPayment", $nvpstr);
        return $resArray;
    }





 

}

?>
