<?php
define('APP_DEBUG', true);
// define('APP_PATH','./Install/');
// define('RUNTIME_PATH', './Install/Runtime/');
if(version_compare(PHP_VERSION,'5.3.0','<'))  die('require PHP > 5.3.0 !');
require_once('./../ThinkPHP/ThinkPHP.php');