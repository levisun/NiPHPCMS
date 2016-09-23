<?php
/**
 *
 * 备份数据库 - 扩展 - 模型
 *
 * @category   Admin\Model
 * @package    NiPHPCMS
 * @author     失眠小枕头 [levisun.mail@gmail.com]
 * @copyright  Copyright (c) 2013, 失眠小枕头, All rights reserved.
 * @version    CVS: $Id: DatabackModel.class.php 2016-05 $
 * @link       http://www.NiPHP.com
 * @since      File available since Release 1.0.1
 */
namespace Admin\Model;
use Think\Model;
class DatabackModel extends Model
{
	protected $tableName = 'admin';

	public function getDataback()
	{
		Vendor('File#class', COMMON_PATH . 'Library');
		$data['list'] = \File::get(RUNTIME_PATH . 'Backup/');
		$data['page'] = '';

		if (empty($data['list'])) {
			return $data;
		}

		rsort($data['list']);

		// 删除过期备份
		$days = strtotime('-180 days');
		foreach ($data['list'] as $key => $value) {
			if (strtotime($value['time']) <= $days) {
				\File::delete(RUNTIME_PATH . 'Backup/' . $value['name']);
				unset($data['list'][$key]);
			} else {
				$data['list'][$key]['id'] = $value['name'];
			}
		}

		$count = count($data['list']);
		$page = new \Think\Page($count, C('PAGE_SHOW_SIZE'));
		$page->rollPage = 5;
		$data['page'] = $page->show();

		$nowPage = !empty($_GET['p']) ? $_GET['p'] - 1 : 0;
		$data['list'] = array_slice($data['list'], $nowPage * 10, 10);

		return $data;
	}

	/**
	 * 下载备份文件
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function down()
	{
		if (empty($_GET['id'])) {
			return L('illegal_operation');
		}

		action('databack_down');

		$fileName = RUNTIME_PATH . 'Backup/' . I('get.id');
		\Org\Net\Http::download($fileName, 'databack.zip');
	}

	/**
	 * 删除备份文件
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function remove()
	{
		if (empty($_GET['id'])) {
			return L('illegal_operation');
		}

		$fileName = RUNTIME_PATH . 'Backup/' . I('get.id');
		Vendor('File#class', COMMON_PATH . 'Library');
		\File::delete($fileName);

		action('databack_remove');
		return true;
	}

	/**
	 * 还原库
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function reduction()
	{
		if (empty($_GET['id'])) {
			return L('illegal_operation');
		}
		Vendor('Pclzip#class', COMMON_PATH . 'Library');
		$zip = new \Pclzip();
		$zip->zipname = RUNTIME_PATH . 'Backup/' . I('get.id');
		$dir = TEMP_PATH . 'BACK' . date('YmdHis') . '/';
		$zip->extract(PCLZIP_OPT_PATH, $dir);

		Vendor('File#class', COMMON_PATH . 'Library');
		$data = \File::get($dir);
		foreach ($data as $key => $value) {
			if ($value['name'] == 'tables.sql') {
				$tables = file_get_contents($dir . $value['name']);
			} else {
				$file[] = file_get_contents($dir . $value['name']);
			}
			$data = strpos($value['name'], 'np_action_log');
		}

		$this->execute($tables);

		foreach ($file as $sql) {
			$this->execute($sql);
		}

		\File::delete($dir);

		action('databack_reduction');
		return true;
	}

	/**
	 * 备份数据库
	 * @access public
	 * @param
	 * @return mixed
	 */
	public function back()
	{
		$tables = $this->getTables();
		$dir = $this->backup($tables);

		Vendor('Pclzip#class', COMMON_PATH . 'Library');
		$zip = new \Pclzip();
		$zip->zipname = RUNTIME_PATH . 'Backup/' . date('YmdHis') . '.zip';
		$zip->create($dir, PCLZIP_OPT_REMOVE_PATH, $dir);

		Vendor('File#class', COMMON_PATH . 'Library');
		\File::delete($dir);

		if ($zip == 0) {
			return $zip->errorInfo(true);
		}

		action('databack_back');
		return true;
	}

	/**
	 * 生成SQL文件
	 * @access private
	 * @param  array   $tables_ 表
	 * @param  intval  $limit_  分文件
	 * @return mixed
	 */
	private function backup($tables_, $limit_=3600)
	{
		if (empty($tables_)) {
			return false;
		}

		$dir = TEMP_PATH . 'BACK' . date('YmdHis') . '/';

		if (!is_dir($dir)) {
			mkdir($dir, 0777, true);
		}

		$TABLESSQL = '';

		foreach ($tables_ as $table) {
			$tableRs = $this->query('SHOW CREATE TABLE `' . $table . '`');

			if (!empty($tableRs[0]['create table'])) {
				$TABLESSQL .= "\r\nDROP TABLE IF EXISTS `{$table}`;\r\n" . $tableRs[0]['create table'] . ';';

				$field = $this->query('SHOW COLUMNS FROM `' . $table . '`');
				$fieldRs = array();
				foreach ($field as $val) {
					$fieldRs[] = $val['field'];
				}

				$count = $this->table($table)->field($fieldRs)->count();
				$count = ceil($count / $limit_);
				for ($i=0; $i < $count; $i++) {
					$firstRow = $i * $limit_;

					$tableDate = $this->table($table)
					->field($fieldRs)
					->limit($firstRow, $limit_)
					->select();

					$INSERTSQL = "INSERT INTO `{$table}` (`" . implode('`,`', $fieldRs) . "`) VALUES ";
					$values = array();
					foreach ($tableDate as $data) {
						$values[] = '(\'' . implode('\',\'', $data) . '\')';
					}
					$INSERTSQL .= implode(',', $values) . ';';

					$num = 40001 + $i;
					file_put_contents($dir . $table . '_' . $num . '.sql', $INSERTSQL);
				}
			}
		}
		file_put_contents($dir . 'tables.sql', $TABLESSQL);

		return $dir;
	}

	/**
	 * 获得库中所有表
	 * @access public
	 * @param
	 * @return array
	 */
	private function getTables()
	{
		$result = $this->query('SHOW TABLES FROM ' . C('DB_NAME'));
		$tables = array();
        foreach ($result as $key => $value) {
            $tables[] = current($value);
        }
        return $tables;
	}
}