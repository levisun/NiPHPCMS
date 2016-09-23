<?php defined('THINK_PATH') or exit();
return  array(
	'DEFAULT_TIMEZONE'      => 'PRC',								// 默认时区
	'DEFAULT_FILTER'        => 'trim,strip_tags,escape_xss',		// 默认过滤
	'PRIMARY_FILTER'        => 'trim,floatval,floor',				// 主键过滤
	'CONTENT_FILTER'        => 'trim,htmlspecialchars,escape_xss',	// 内容过滤

	/* 数据库设置 */
	'DB_TYPE'         => 'mysql',	// 数据库类型
	'DB_HOST'         => '',		// 服务器地址
	'DB_NAME'         => '',		// 数据库名
	'DB_USER'         => '',		// 用户名
	'DB_PWD'          => '',		// 密码
	'DB_PORT'         => '3306',	// 端口
	'DB_PREFIX'       => 'np_',		// 数据库表前缀
	'DB_PARAMS'       => array(),	// 数据库连接参数
	'DB_DEBUG'        => false,		// 数据库调试模式 开启后可以记录SQL日志
	'DB_FIELDS_CACHE' => true,		// 启用字段缓存
	'DB_CHARSET'      => 'utf8',	// 数据库编码默认采用utf8
	'DB_DEPLOY_TYPE'  => 0,			// 数据库部署方式:0 集中式(单一服务器),1 分布式(主从服务器)
	'DB_RW_SEPARATE'  => false,		// 数据库读写是否分离 主从式有效
	'DB_MASTER_NUM'   => 1,			// 读写分离后 主服务器数量
	'DB_SLAVE_NO'     => '',		// 指定从服务器序号

	/* 数据缓存设置 */
	'DATA_CACHE_TIME'     => 10800,		// 数据缓存有效期 0表示永久缓存
	'DATA_CACHE_COMPRESS' => false,		// 数据缓存是否压缩缓存
	'DATA_CACHE_CHECK'    => true,		// 数据缓存是否校验缓存
	'DATA_CACHE_TYPE'     => 'File',	// 数据缓存类型
	'DATA_CACHE_SUBDIR'   => true,		// 使用子目录缓存
	'DATA_PATH_LEVEL'     => 1,			// 子目录缓存级别
	'DATA_CRYPT_TYPE'    => 'NIPHP',	// 数据加密方式

	/* 错误设置 */
	'ERROR_MESSAGE'    => '页面错误！请稍后再试～',	// 错误显示信息,非调试模式有效
	'ERROR_PAGE'       => '/error.html',		// 错误定向页面
	'SHOW_ERROR_MSG'   => APP_DEBUG,			// 显示错误信息
	'TRACE_MAX_RECORD' => 100,					// 每个级别的错误信息 最大记录数

	/* 日志设置 */
	'LOG_RECORD' => true,			// 记录日志
	'LOG_TYPE'   => 'File',			// 日志记录类型 默认为文件方式
	'LOG_LEVEL'  => 'EMERG,ALERT,CRIT,ERR,WARN,NOTICE,DEBUG,SQL',	// 允许记录的日志级别
	'LOG_EXCEPTION_RECORD' => true,	// 记录异常信息日志

	'SESSION_AUTO_START' => true,	// 开启Session

	'TMPL_CONTENT_TYPE'    => 'text/html',		// 默认模板输出类型
	'TMPL_ACTION_ERROR'    => '/dispatch_jump',	// 默认错误跳转对应的模板文件
	'TMPL_ACTION_SUCCESS'  => '/dispatch_jump',	// 默认成功跳转对应的模板文件
	'TMPL_TEMPLATE_SUFFIX' => '.html',			// 默认模板文件后缀
	'TMPL_FILE_DEPR'       => '/',				// 模板文件CONTROLLER_NAME与ACTION_NAME之间的分割符

	/* 布局设置 */
	'TMPL_ENGINE_TYPE'     => 'Think',			// 默认模板引擎 以下设置仅对使用Think模板引擎有效
	'TMPL_CACHFILE_SUFFIX' => '.php',			// 默认模板缓存后缀
	'TMPL_DENY_FUNC_LIST'  => 'echo,exit',		// 模板引擎禁用函数
	'TMPL_DENY_PHP'        => false,			// 默认模板引擎是否禁用PHP原生代码
	'TMPL_L_DELIM'         => '{',				// 模板引擎普通标签开始标记
	'TMPL_R_DELIM'         => '}',				// 模板引擎普通标签结束标记
	'TMPL_VAR_IDENTIFY'    => 'array',			// 模板变量识别。留空自动判断,参数为'obj'则表示对象
	'TMPL_STRIP_SPACE'     => true,				// 是否去除模板文件里面的html空格与换行
	'TMPL_CACHE_ON'        => false,			// 是否开启模板编译缓存,设为false则每次都会重新编译
	'TMPL_CACHE_TIME'      => 0,				// 模板缓存有效期 0 为永久，(以数字为值，单位:秒)
	'TMPL_LAYOUT_ITEM'     => '{__CONTENT__}',	// 布局模板的内容替换标识
	'LAYOUT_ON'            => true,				// 是否启用布局
	'LAYOUT_NAME'          => 'layout',			// 当前布局名称 默认为layout
	'TMPL_PARSE_STRING'    => array(
		'__STATIC__' => './Static/',
		),

	'URL_CASE_INSENSITIVE' => true,				// 不区分大小写
	'URL_MODEL'            => 0,
	'URL_HTML_SUFFIX'      => 'html',			// URL伪静态后缀设置

	'COOKIE_PREFIX'   => 'NP_',
	'SESSION_PREFIX'  => '',
	'LANG_SWITCH_ON'  => true,					// 语言包设置
	'SHOW_PAGE_TRACE' => APP_DEBUG,				// 显示页面Trace信息
	'TOKEN_ON'        => true,					// 表单令牌
	'LOAD_EXT_CONFIG' => 'db,lang',
	'page_show_size'  => 10,
);
