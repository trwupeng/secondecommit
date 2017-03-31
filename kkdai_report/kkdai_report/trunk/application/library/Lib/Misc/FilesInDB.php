<?php
/**
 * Description of FilesInDB
 *
 * @author simon.wang
 */
class FilesInDB {
	/**
	 * 
	 * @param \Sooh\DB\Interfaces\All $db
	 * @param string $fileData
	 * @param string $filename
	 */
	public function save($db,$fileData,$filename=null) {
		if(empty($filename)){
			$filename='unknown_'.date('Ymd').'_'.rand(1,99999999);
		}

		$db->execCustom(array('sql'=>'insert IGNORE into tb_files values(\''.$filename.'\',null)'));
		$db->execCustom(array('sql'=>"update tb_files set fileData='".  addslashes($fileData)."' where fileId='".$filename."'"));
;
	}
}
