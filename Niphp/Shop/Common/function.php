<?php defined('THINK_PATH') or die();

/**
 * 数字转钱
 * @param  intval $IMoeny_
 * @return string
 */
function money_formats($money_)
{
	$money_ = floor(floatval($money_));
	return number_format($money_ / 100, 2);
}

/**
 * 生成订单号
 * @param
 * @return string
 */
function order_no()
{
	$orderNo = date('ymdHis');
	$orderNo .= str_pad(microtime() * 1000000, 6, 0, STR_PAD_LEFT);
	$orderNo .= mt_rand(111, 999);
	$orderNo .= cookie(C('USER_AUTH_KEY'));
	return $orderNo;
}