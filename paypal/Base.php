<?php
/**
 * class Base
 * 
 */
class Base {

    protected $_sandboxFlag = true;
    protected $_api_username = "mdbald_1287976381_biz_api1.michaelbalderas.com";
    protected $_api_password = "1287976406";
    protected $_api_signature = "APOxIKm-Fx0tSYmLLbuPFN42APwdAhhNTtvJ8YhTD2ALC9poKmbhBaf6";
    protected $_api_version = "64"; 
    protected $_api_endPoint;
    protected $_paypal_Url;
    protected $_return_url;
    protected $_cancel_url;
    protected $_sBNCode = "PP-ECWizard";
    protected $USE_PROXY = false;

    public function set_api_username($_api_username) {
        $this->_api_username = $_api_username;
    }

    public function set_api_password($_api_password) {
        $this->_api_password = $_api_password;
    }

    public function set_api_signature($_api_signature) {
        $this->_api_signature = $_api_signature;
    }

    public function set_api_version($_api_version) {
        $this->_api_version = $_api_version;
    }

    public function set_return_url($_return_url) {
        $this->_return_url = $_return_url;
    }

    public function set_cancel_url($_cancel_url) {
        $this->_cancel_url = $_cancel_url;
    }

    function __construct() {
        $this->setEnv();
    }

    function RedirectToPayPal($token) {
        header("Location: " . $this->_cancel_url . $token);
    }

    final function setEnv() {
        if ($this->_sandboxFlag === true) {
            $this->_api_endPoint = "https://api-3t.sandbox.paypal.com/nvp";
            $this->_paypal_Url = "https://www.sandbox.paypal.com/webscr?cmd=_express-checkout&token=";
        } else {
            $this->_api_endpoint = "https://api-3t.paypal.com/nvp";
            $this->_paypal_Url = "https://www.paypal.com/cgi-bin/webscr?cmd=_express-checkout&token=";
        }
        if (session_id() === "")
            session_start();
    }

    function hash_call($methodName, $nvpStr) {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_api_endPoint);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);

        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);

        /*if ($USE_PROXY)
            curl_setopt($ch, CURLOPT_PROXY, $PROXY_HOST . ":" . $PROXY_PORT);*/
        $nvpreq = "METHOD=" . urlencode($methodName) . "&VERSION=" . urlencode($this->_api_version) .
                "&PWD=" .
                urlencode($this->_api_password) . "&USER=" . urlencode($this->_api_username) . "&SIGNATURE=" .
                urlencode($this->_api_signature) . $nvpStr . "&BUTTONSOURCE=" . urlencode($this->_sBNCode);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $nvpreq);

        $response = curl_exec($ch);

        $nvpResArray = $this->deformatNVP($response);
        $nvpReqArray = $this->deformatNVP($nvpreq);
        $_SESSION['nvpReqArray'] = $nvpReqArray;
        if (curl_errno($ch)) {

            $_SESSION['curl_error_no'] = curl_errno($ch);
            $_SESSION['curl_error_msg'] = curl_error($ch);
        } else {

            curl_close($ch);
        }
        return $nvpResArray;
    }
 
    function deformatNVP($nvpstr) {
        $intial = 0;
        $nvpArray = array();
        while (strlen($nvpstr)) {
            $keypos = strpos($nvpstr, '=');

            $valuepos = strpos($nvpstr, '&') ? strpos($nvpstr, '&') : strlen($nvpstr);
       
            $keyval = substr($nvpstr, $intial, $keypos);
            $valval = substr($nvpstr, $keypos + 1, $valuepos - $keypos - 1);

            $nvpArray[urldecode($keyval)] = urldecode($valval);
            $nvpstr = substr($nvpstr, $valuepos + 1, strlen($nvpstr));
        }
        return $nvpArray;
    }

}

?>
