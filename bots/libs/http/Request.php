<?php

/**
 * Description of Client
 *
 * @author root
 */

namespace HTTP;

class Request {

    private static function __formatTimeout($timeout = 0) {
        $timeout = (float)$timeout;
        if ($timeout < 0.1) {
            $timeout = 60;
        }

        return $timeout;
    }

    private static function __parseResponse($body, $header) {
        $status_code = 0;
        $content_type = '';

        if (is_array($header) && count($header) > 0) {
            foreach ($header as $v) {
                if (substr($v, 0, 4) === 'HTTP' && strrpos($v, ' ')) {
                    $status_code = (int) substr($v, strpos($v, ' '), 4);
                } else if (strncasecmp($v, 'Content-Type:', 13) === 0) {
                    $content_type = $v;
                }
            }
        }

        return new \HTTP\Response($status_code, $content_type, $body, $header);
    }

    public function get($url, $timeout = 0) {
        $timeout = (float)$timeout;
        $context = stream_context_create();
        stream_context_set_option($context, 'http', 'timeout', self::__formatTimeout($timeout));
        $http_response_header =
                $res_body = file_get_contents($url, false, $context);
        return self::__parseResponse($res_body, $http_response_header);
    }

    public static function head($url, $timeout = 0) {
        $context = stream_context_create();
        stream_context_set_option($context, [
            'http' => [
                'method' => 'HEAD',
                'timeout' => self::__formatTimeout($timeout)
            ]
        ]);
        $http_response_header = NULL; 
        $res_body =
                file_get_contents($url, false, $context);
        return self::__parseResponse($res_body, $http_response_header);
    }

}

?>
