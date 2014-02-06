<?php
class ServerSide {

	protected $_provider;

	function getNewConsumerKey() {
		$fp = fopen ( '/dev/urandom', 'rb' );
		$entropy = fread ( $fp, 32 );
		fclose ( $fp );

		$entropy .= uniqid ( mt_rand (), true );
		$hash = sha1 ( $entropy );

		$this->_provider = new  OAuthProvider();

		$t = $this->_provider->generateToken(4);

		echo strlen($t),  PHP_EOL;
		echo bin2hex($t), PHP_EOL;

		return array (
				substr ( $hash, 0, 30 ),
				substr ( $hash, 30, 10 )
		);
	}


	function endPoint()
	{
		try {
			$this->_provider = new OAuthProvider();
			$this->_provider->consumerHandler(array($this,'lookupConsumer'));
			$this->_provider->timestampNonceHandler(array($this,'timestampNonceChecker'));
			$this->_provider->tokenHandler(array($this,'tokenHandler'));
			$this->_provider->setParam('kohana_uri', NULL);
			$this->_provider->setRequestTokenPath('/v1/oauth/request_token');
			$this->_provider->checkOAuthRequest();
		} catch (OAuthException $E) {
			echo OAuthProvider::reportProblem($E);
			$this->oauth_error = true;
		}
	}



	function lookupConsumer()
	{
		$consumer = ORM::Factory("consumer", $provider->consumer_key);
		if($provider->consumer_key != $consumer->consumer_key) {
			return OAUTH_CONSUMER_KEY_UNKNOWN;
		} else if($consumer->key_status != 0) {
			return OAUTH_CONSUMER_KEY_REFUSED;
		}
		$provider->consumer_secret = $consumer->secret;
		return OAUTH_OK;
	}
}

?>