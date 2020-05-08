<?php

use Core\Response;

/**
 * Return a view
 */
function response($object = null, $http_status = 200, array $headers = null)
{
    ob_start();
    if ($object == null) {
        return Response::response();
    } else {
        return Response::response($object, $http_status, $headers);
    }
    ob_get_contents();
    ob_end_flush();
}
