<?php

use Config\Config;

/**
 * Return a configuration value
 */
function config($name = '')
{
    return Config::config($name);
}
