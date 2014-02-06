<?php
/**
 * NewsLetters class based on campaign monitor wrapper it allow create and
 * send newsletter. for that get NewsLetters instance with apropriate setting
 * Ex .
 * $path = 'http://local.helpers.com/news.php';
 * $campaign = new NewsLetters('api key', 'list id', 'template path', $params);
 * $campaign->run();
 *
 * @author Thierno, Bah <<thierno.i.bah@gmail.com>>
 * @version 1.0, Janvier 2014
 */

/**
 * include campaign monitor campaigns class
 */
require_once dirname ( __FILE__ ) . '/csrest_campaigns.php';

/**
 * include campaign monitor csrest_campaigns class
 */
require_once dirname ( __FILE__ ) . '/csrest_general.php';
class NewsLetters {

	/**
	 * protected string $_api_key campaign monitor api key
	 */
	protected $_api_key;

	/**
	 * protected string $_template_path newsletters template path
	 */
	protected $_template_path;

	/**
	 * protected string $_campaign_id
	 */
	protected $_campaign_id = null;

	/**
	 * protected array $_params campaign config fields
	 */
	protected $_params = array ();

	/**
	 * protected $_listId campaign list id
	 */
	protected $_listId;

	/**
	 * __construct method
	 *
	 * @param string $api_key
	 * @param string $listID
	 * @param string $template_path
	 * @param array $params
	 *        	Ex: $params = array(
	 *        	'subject' => 'Campaign subjet *',
	 *        	'name' => 'Campaign name *',
	 *        	'fromName' => 'Sender name *',
	 *        	'FromEmail' => 'Sender email *',
	 *        	'replyEmail' => 'reply Email'
	 *        	);
	 *        	(*) required
	 *
	 * @param string $campaign_id
	 * @return void
	 */
	function __construct($api_key, $listID, $template_path, $params, $campaign_id = null) {
		$this->_api_key = $api_key;
		$this->_template_path = $template_path;
		$this->_listId = $listID;
		$this->_params = $params;

		if (! empty ( $campaign_id )) {
			$this->setCampaignId ( $campaign_id );
		}
	}

	/**
	 * getApiKey method
	 *
	 * @return api key
	 */
	public function getApiKey() {
		return $this->_api_key;
	}

	/**
	 * getTemplatePath method
	 *
	 * @return template path
	 */
	public function getTemplatePath() {
		return $this->_template_path;
	}

	/**
	 * getCampaignId method
	 *
	 * @return campaign_id
	 */
	public function getCampaignId() {
		return $this->_campaign_id;
	}

	/**
	 * setCampaignId method
	 *
	 * @return $this
	 */
	public function setCampaignId($campaign_id) {
		$this->_campaign_id = $_campaign_id;
		return $this;
	}

	/**
	 * getReplyToEmail method
	 *
	 * @return reply
	 */
	function getReplyToEmail() {
		if (! empty ( $this->_params ['replyEmail'] )) {
			return $this->_params ['replyEmail'];
		} else {
			return $this->_params ['fromEmail'];
		}
	}

	/**
	 * createCampaign method create campaign, uncomment SegmentIDs if segment used
	 *
	 * @return void
	 */
	function createCampaign() {
		if (empty ( $this->_campaign_id )) {

			$wrap = new CS_REST_Campaigns ( NULL, $this->_api_key );
			$result = $wrap->create ( $this->getClientId (), array (
					'Subject' => $this->_params ['subject'],
					'Name' => $this->_params ['name'] . ' ' . date ( 'Y-m-d h:i s' ),
					'FromName' => $this->_params ['fromName'],
					'FromEmail' => $this->_params ['fromEmail'],
					'ReplyTo' => $this->getReplyToEmail (),
					'HtmlUrl' => $this->_template_path,
					'TextUrl' => $this->_template_path,
					'ListIDs' => array (
							$this->_listId
					)
			// 'SegmentIDs' => array('9a0ab5aeda4e73e2b87ce153a1e5d90f')
						) );

			if ($result->was_successful ()) {
				$this->_campaign_id = $result->response;
			} else {
				echo 'Failed with code ' . $result->http_status_code . "\n<br />";
				/*
				 * "<pre>"; var_dump ( $result->response ); echo '</pre>';
				 */
			}
		}
	}

	/**
	 * getClientId method
	 *
	 * @return string clientId
	 *
	 */
	function getClientId() {
		$auth = array (
				'api_key' => $this->_api_key
		);
		$wrap = new CS_REST_General ( $auth );

		$result = $wrap->get_clients ();
		if ($result->was_successful ()) {
			return $result->response [0]->ClientID;
		}
	}

	/**
	 * sendCampaign method
	 *
	 * @return void
	 */
	function sendCampaign() {
		$wrap = new CS_REST_Campaigns ( $this->_campaign_id, $this->_api_key );

		$result = $wrap->send ( array (
				'ConfirmationEmail' => 'ramifood@gmail.com',
				'SendDate' => 'Immediately'
		) );

		if ($result->was_successful ()) {
			echo "Scheduled with code\n<br />" . $result->http_status_code;
		} else {
			echo 'Failed with code ' . $result->http_status_code . "\n<br />";
			/*
			 * "<pre>"; var_dump ( $result->response ); echo '</pre>';
			 */
		}
	}

	/**
	 * run method, create and send campaign
	 *
	 * @return void
	 */
	function run() {
		$this->createCampaign ();
		$this->sendCampaign ();
	}
}