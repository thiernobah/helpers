<?php

require_once dirname(__FILE__).'/libs/http/Request.php';
require_once dirname(__FILE__).'/libs/http/Response.php';


$responpe = \HTTP\Request::head('http://www.maliactu.net');

echo '<pre>';
print_r($responpe);
echo '</pre>';