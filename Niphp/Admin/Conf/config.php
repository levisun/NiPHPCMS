<?php defined('THINK_PATH') or die();
return array(
	'DEFAULT_THEME' => 'npv1608',

	'settings' => 'icon-cogs',
	'theme' => 'icon-dashboard',
	'category' => 'icon-reorder',
	'content' => 'icon-edit',
	'user' => 'icon-group',
	'wechat' => 'icon-comments',
	'shop' => 'icon-shopping-cart',
	'expand' => 'icon-wrench',

	'SESSION_OPTIONS' => array(
		// 'expire' => '1140'
		),

	'DEFAULT_CONTROLLER' => 'Account',
	'DEFAULT_ACTION' => 'login',
	'DEFAULT_LANG' => 'zh-cn',

	// RBAC
	'USER_AUTH_ON' => 1,
	'USER_AUTH_TYPE' => 2,
	'USER_AUTH_KEY' => 'USER_ID',
	'USER_AUTH_GATEWAY' => '?m=admin&c=account&a=login',
	'NOT_AUTH_MODULE' => 'Account',
	'NOT_AUTH_ACTION' => 'login,verify',
	'RBAC_ROLE_TABLE' => 'np_role',
	'RBAC_USER_TABLE' => 'np_role_admin',
	'RBAC_ACCESS_TABLE' => 'np_access',
	'RBAC_NODE_TABLE' => 'np_node',
	);