<?php defined('THINK_PATH') or die();

/**
 * 非法操作验证
 * @param
 * @return mixed
 */
function illegal()
{
	if (empty($_GET['id']) || !is_numeric($_GET['id'])) {
		return L('illegal_operation');
	}
	return false;
}

/**
 * 栏目ID是否为空
 * 用于自定义字段 和 分类
 * @param array $data_
 * @return void
 */
function isCategoryId($data_)
{
	if (is_array($data_)) {
		return !empty($data_[0]);
	}
	return false;
}

/**
 * 执行日志
 * @param string $action_   行为名称
 * @param intval $recordId_ 数据ID
 * @param string $remark_   备注
 * @return void
 */
function action($action_, $recordId_=0, $remark_='')
{
	$action = M('action')->field('id')->where(array('name' => $action_))->find();
	if (empty($action['id'])) {
		return false;
	}

	$dir = '../../../../';
	$ip = new \Org\Net\IpLocation($dir . COMMON_PATH . 'Library/UTFWry.dat');
	$area = $ip->getlocation();

	$data = array(
		'action_id' => $action['id'],
		'user_id' => session(C('USER_AUTH_KEY')),
		'action_ip' => $area['ip'] . '[' . $area['country'] . $area['area'] . ']',
		'model' => CONTROLLER_NAME . '-' . ACTION_NAME,
		'record_id' => $recordId_,
		'remark' => $remark_,
		'create_time' => time()
		);
	M('action_log')->data($data)->add();

	$map = array('create_time' => array('ELT', strtotime('-30 days')));
	M('action_log')->where($map)->delete();
}