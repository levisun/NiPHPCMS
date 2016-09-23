<?php defined('THINK_PATH') or die();

/**
 * 访问
 * @param
 * @return void
 */
function visit()
{
	// 删除过期的访问日志(保留三个月)
	$table = C('DB_PREFIX') . 'visit';
	$map = array('date' => array('ELT', strtotime('-90 days')));
	M()->table($table)->where($map)->delete();

	$key = is_spider();
	if ($key !== false) {
		return false;
	}

	$dir = '../../../../';
	$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
	$area = $ip->getlocation();
	if ($area['ip'] == '0.0.0.0' || $area['ip'] == '127.0.0.1') {
		return false;
	}

	$field = array('date', 'ip', 'ipattr', 'count');
	$map = array(
		'ip' => $area['ip'],
		'ipattr' => $area['country'] . $area['area'],
		'date' => strtotime(date('Y-m-d'))
		);
	$isKey = M()->table($table)->where($map)->find();
	if (empty($isKey)) {
		M()->table($table)->field($field)->data($map)->add();
	} else {
		M()->table($table)->field($field)->where($map)->setInc('count', 1);;
	}
}

/**
 * 搜索引擎
 * @param
 * @return void
 */
function searchengine()
{
	// 删除过期的搜索日志(保留三个月)
	$table = C('DB_PREFIX') . 'searchengine';
	$map = array('date' => array('ELT', strtotime('-90 days')));
	M()->table($table)->where($map)->delete();

	$key = is_spider();
	if ($key === false) {
		return false;
	}

	$field = array('date', 'name', 'count');
	$map = array('name' => $key, 'date' => strtotime(date('Y-m-d')));
	$isKey = M()->table($table)->where($map)->find();
	if (empty($isKey)) {
		M()->table($table)->field($field)->data($map)->add();
	} else {
		M()->table($table)->field($field)->where($map)->setInc('count', 1);;
	}
}

/**
 * 删除过期日志
 * @param
 * @return void
 */
function removeLogs()
{
	if (mt_rand(1, 10) == 1) {
		return ;
	}
	Vendor('File#class', COMMON_PATH . 'Library');
	$logs = \File::get(LOG_PATH . MODULE_NAME . '/');
	// 删除过期日志
	$days = strtotime('-90 days');
	foreach ($logs as $key => $value) {
		if (strtotime($value['time']) <= $days) {
			\File::delete(LOG_PATH . MODULE_NAME . '/' . $value['name']);
		}
	}
}

/**
 * 判断搜索引擎蜘蛛
 * @param
 * @return mixed
 */
function is_spider()
{
	if (empty($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}

	$searchengine = array(
		'GOOGLE' => 'googlebot',
		'GOOGLE ADSENSE' => 'mediapartners-google',
		'BAIDU' => 'baiduspider+',
		'MSN' => 'msnbot',
		'YODAO' => 'yodaobot',
		'YAHOO' => 'yahoo! slurp;',
		'Yahoo China' => 'yahoo! slurp china;',
		'IASK' => 'iaskspider',
		'SOGOU' => 'sogou web spider',
		'SOGOU' => 'sogou push spider'
		);
	$spider = strtolower($_SERVER['HTTP_USER_AGENT']);
	foreach ($searchengine as $key => $value) {
		if (strpos($spider, $value) !== false) {
			return $key;
		}
	}
	return false;
}

/**
 * 是否微信端访问
 * @param
 * @return boolean
 */
function is_wechat()
{
	if (empty($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}
	if (strpos($_SERVER['HTTP_USER_AGENT'], 'MicroMessenger')) {
		return true;
	}
	return false;
}

/**
 * 是否手机端访问
 * @param
 * @return boolean
 */
function is_mobile()
{
	if (empty($_SERVER['HTTP_USER_AGENT'])) {
		return false;
	}
	$uachar = '/(nokia|sony|ericsson|mot|samsung|sgh|lg|philips|panasonic|alcatel|lenovo|cldc|midp|mobile|wap|Android|ucweb)/i';
	$ua = strtolower($_SERVER['HTTP_USER_AGENT']);
	if (preg_match($uachar, $ua) && !strpos(strtolower($_SERVER['REQUEST_URI']), 'wap')) {
		return true;
	}
	return false;
}

/**
 * 字符串截取，支持中文和其他编码
 * @param string $str 需要转换的字符串
 * @param string $start 开始位置
 * @param string $length 截取长度
 * @param string $charset 编码格式
 * @param string $suffix 截断显示字符
 * @return string
 */
function msubstr($str, $start=0, $length, $charset="utf-8", $suffix=true)
{
	return Org\Util\String::msubstr($str, $start, $length, $charset, $suffix);
}

/**
 * 过滤XSS
 * @param  string $string_
 * @return string
 */
function escape_xss($string_)
{
	// 过滤PHP
	$string_ = preg_replace('/<\?php(.*?)\?>/si', '', $string_);
	$string_ = preg_replace('/<\?(.*?)\?>/si', '', $string_);
	$string_ = preg_replace('/<%(.*?)%>/si', '', $string_);
	$string_ = preg_replace('/<\?php|<\?|\?>|<%|%>/si', '', $string_);

	// 过滤javascript
	$parm = array(
		'javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml',
		'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame',
		'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'
		);
	foreach ($parm as $val) {
		$preg = '/<(' . $val . '.*?)>(.*?)<(\/' . $val . '.*?)>/si';
		$string_ = preg_replace($preg, '', $string_);

		$preg = '/<(\/?' . $val . '.*?)>/si';
		$string_ = preg_replace($preg, '', $string_);
	}
	$parm = array(
		'onabort', 'onactivate', 'onafterprint', 'onafterupdate',
		'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate',
		'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload',
		'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange',
		'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut',
		'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick',
		'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave',
		'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate',
		'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout',
		'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete',
		'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave',
		'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel',
		'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange',
		'onreadystatechange', 'onreset', 'onresize', 'onresizeend',
		'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete',
		'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange',
		'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'
		);
	foreach ($parm as $val) {
		$preg = '/(' . $val . '.*?)["|\'](.*?)["|\']/si';
		$string_ = preg_replace($preg, '', $string_);
	}

	// 转义特殊字符
	$strtr = array(
		// PHP_EOL . PHP_EOL => '',
		'%20' => '',
		'"' => '&quot;', '\'' => '&#39;', '*' => '&lowast;', '`' => '&acute;',
		'￥' => '&yen;', '™' => '&trade;', '®' => '&reg;', '©' => '&copy;',
		'<' => '&lt;', '>' => '&gt;',

		'０' => '0', '１' => '1', '２' => '2', '３' => '3', '４' => '4',
		'５' => '5', '６' => '6', '７' => '7', '８' => '8', '９' => '9',
		'Ａ' => 'A', 'Ｂ' => 'B', 'Ｃ' => 'C', 'Ｄ' => 'D', 'Ｅ' => 'E',
		'Ｆ' => 'F', 'Ｇ' => 'G', 'Ｈ' => 'H', 'Ｉ' => 'I', 'Ｊ' => 'J',
		'Ｋ' => 'K', 'Ｌ' => 'L', 'Ｍ' => 'M', 'Ｎ' => 'N', 'Ｏ' => 'O',
		'Ｐ' => 'P', 'Ｑ' => 'Q', 'Ｒ' => 'R', 'Ｓ' => 'S', 'Ｔ' => 'T',
		'Ｕ' => 'U', 'Ｖ' => 'V', 'Ｗ' => 'W', 'Ｘ' => 'X', 'Ｙ' => 'Y',
		'Ｚ' => 'Z', 'ａ' => 'a', 'ｂ' => 'b', 'ｃ' => 'c', 'ｄ' => 'd',
		'ｅ' => 'e', 'ｆ' => 'f', 'ｇ' => 'g', 'ｈ' => 'h', 'ｉ' => 'i',
		'ｊ' => 'j', 'ｋ' => 'k', 'ｌ' => 'l', 'ｍ' => 'm', 'ｎ' => 'n',
		'ｏ' => 'o', 'ｐ' => 'p', 'ｑ' => 'q', 'ｒ' => 'r', 'ｓ' => 's',
		'ｔ' => 't', 'ｕ' => 'u', 'ｖ' => 'v', 'ｗ' => 'w', 'ｘ' => 'x',
		'ｙ' => 'y', 'ｚ' => 'z',
		'（' => '(', '）' => ')', '〔' => '[', '〕' => ']', '【' => '[',
		'】' => ']', '〖' => '[', '〗' => ']', '｛' => '{', '｝' => '}',
		'％' => '%', '＋' => '+', '—' => '-', '－' => '-', '～' => '-',
		'：' => ':', '？' => '?', '！' => '!', '…' => '-', '‖' => '|',
		'”' => '&quot;', '“' => '&quot;',  '’' => '&acute;', '‘' => '&acute;',
		'｜' => '|', '〃' => '&quot;', '　' => ' '
		);
	$string_ = strtr($string_, $strtr);
	return $string_;
}

/**
 * 获得域名
 * @param
 * @return string
 */
function domain()
{
	if (isset($_SERVER['HTTPS']) && 'on' == $_SERVER['HTTPS']) {
		$url = 'https://';
	} else {
		$url = 'http://';
	}
	if ('80' != $_SERVER['SERVER_PORT']) {	// 是否是默认端口
		return $url . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname(_PHP_FILE_) . '/';
	} else {
		if (strlen(dirname(_PHP_FILE_)) > 1) {
			return $url . $_SERVER['SERVER_NAME'] . dirname(_PHP_FILE_) . '/';
		} else {
			return $url . $_SERVER['SERVER_NAME'] . '/';
		}
	}
}

/**
 * 校验验证码
 */
function check_verify($code_, $id_='')
{
	$verify = new \Think\Verify();
	return $verify->check($code_, $id_);
}