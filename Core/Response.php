<?php

namespace Core;

use Core\HTTP;

class Response
{
    /**
     * Return a response
     */
    public final static function response($object = null, $http_status = 200, array $headers = null)
    {
        //verify if has sent object
        if ($object != null) {
            return self::json($object, $http_status, $headers);
        }
        return new static();
    }

    public final static function json($content, int $http_status = 200, array $headers = null)
    {
        header('Content-type: application/json');
        header("HTTP/1.1 $http_status " . HTTP::getStatusHTTP($http_status));
        if ($headers != null && is_array($headers)) {
            foreach ($headers as $header) {
                header($header);
            }
        }
        echo json_encode($content, JSON_UNESCAPED_UNICODE);
    }
}
//header('Content-type: application/json');
 //   header("HTTP/1.1 404 Not Found");
  //  echo json_encode($object, JSON_UNESCAPED_UNICODE);