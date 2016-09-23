<?php defined('THINK_PATH') or die();
return array(
	'app_begin' => array('Behavior\CheckLangBehavior'),
	'view_filter' => array('Behavior\TokenBuildBehavior'),
);