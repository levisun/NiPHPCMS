<?php
if(version_compare(PHP_VERSION,'5.3.0','<')) die('require PHP > 5.3.0 !');
define('APP_DEBUG', true);
define('SYS_VERSION', '1.0.160917');
define('APP_PATH','./Niphp/');
define('RUNTIME_PATH', './Runtime/');
define('HTML_PATH', './html/');
require_once('./ThinkPHP/ThinkPHP.php');