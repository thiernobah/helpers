<?php

require_once '../../csrest_general.php';

$auth = array('api_key' => '69bbaa5baecb1b1c563af735031d2b54');
$wrap = new CS_REST_General($auth);

$result = $wrap->get_clients();


echo "Result of /api/v3/clients\n<br />";
if($result->was_successful()) {
    echo "Got clients\n<br /><pre>";
    var_dump($result->response);
} else {
    echo 'Failed with code '.$result->http_status_code."\n<br /><pre>";
    var_dump($result->response);
}
echo '</pre>';