<?php

class Api {
	
	protected function set_content(
				$pagetitle, 
				$longtitle, 
				$alias, 
				$parent, 
				$template, 
				$content,
				$description
	) {
		$out = $this->check_isset_content($alias);
		if($out > 0) { 
		} else {
			$out = $this->insert_content(
				$pagetitle, 
				$longtitle, 
				$alias, 
				$parent, 
				$template, 
				$content,
				$description
			);
		}
		return $out;
	}

	protected function get_content($where = '', $limit = '', $active = 1){
		global $modx;

		$where_active = ' published = 1 AND deleted = 0 AND id > 1 ';

		if($active > 0) {
			if(empty($where)) {
				$where = $where_active;
			} else {
				$where = $where.' AND '.$where_active; 
			}
		}

		$limit = empty($limit) ? ' LIMIT 1000' : ' LIMIT '.$limit;
		 
		$out = array();
		$sql = 'SELECT * FROM modx_site_content WHERE '.$where.$limit;

		$get_content = $modx->db->query($sql);

		while($row = $modx->db->getRow($get_content)){ 
		 	$out[] = $row;
		}

		return $out;
	}

	protected function set_tv($tmplvarid, $contentid, $value) { 
 		$out = 0;

		if($this->check_insert_tv($contentid, $tmplvarid) > 0) {
			if($this->update_tv($tmplvarid, $contentid, $value)) {
				$out = 1;
			}
		} else {
			if($this->insert_tv($tmplvarid, $contentid, $value)) {
				$out = 1;
			}
		} 

		return $out;
	}

	private function check_isset_content($alias = '') {
		global $modx;
		return  $modx->db->getValue(
 					$modx->db->select(
 						"id",
 						$modx->getFullTableName("site_content"),
 						 "alias = '".$alias."'"
 			)
		);
	}

	protected function update_content($fields = array(), $where = '') {
		global $modx;

		$set_field = array();

		foreach ($fields as $key => $value) {
			$set_field[] = $key." = '".$modx->db->escape($value)."'";
		}

		$set_field = implode(', ',$set_field);

		return $modx->db->update(
			$set_field, 
			$modx->getFullTableName("site_content"), 
			$where
		);
	}

	protected function insert_content(
		$pagetitle = '', 
		$longtitle = '', 
		$alias = '', 
		$parent = '', 
		$template = 1, 
		$content = '',
		$description = ''
	) {
		global $modx;

		if(
			$modx->db->insert(
			array(
					'pagetitle' => trim($modx->db->escape($pagetitle)), 
					'longtitle' => trim($modx->db->escape($longtitle)), //для вставки кода группы
					'alias' => trim($alias),
					'published' => 1,
					'parent' => intval($parent),
					'isfolder' => 0,
					'introtext' => NULL,
					'content' => $modx->db->escape($content),
					'template' => intval($template),
					'menuindex' => 0,
					'createdon' => time(),
					'hidemenu' => 0,
					'description' =>  $description
				), $modx->getFullTableName('site_content'))
		) {
			return $modx->db->getInsertId();
		} else {
			return false;
		}

	}

	private function check_insert_tv($contentid, $tmplvarid) {
		global $modx;
		return  $modx->db->getValue(
 					$modx->db->select(
 						"id",
 						$modx->getFullTableName("site_tmplvar_contentvalues"),
 						 "tmplvarid = ".$tmplvarid." and value > '' AND contentid = ".$contentid
 			)
		);
	}

	protected function insert_tv($tmplvarid, $contentid, $value) {
		global $modx;

		return $modx->db->insert(
			array(
				'tmplvarid' => $modx->db->escape($tmplvarid),
 				'contentid' => $modx->db->escape($contentid),
 				 'value' => $modx->db->escape( $value)
 			), 
 			$modx->getFullTableName('site_tmplvar_contentvalues')
 		);
	}

	protected function update_tv($tmplvarid, $contentid, $value) {
		global $modx;

		return $modx->db->update(
				"value = '".$modx->db->escape($value)."'", 
			$modx->getFullTableName("site_tmplvar_contentvalues"), 
			"`tmplvarid` = ".intval($tmplvarid)." AND
			 `contentid` = ".intval($contentid)
		);
	}

	protected function file_get_contents_curl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		 //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		 //разрешаем перенаправление на полученный в заголовке URL
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		//имитируем браузер опера
		 curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');

		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	protected function get_meta($content) { 

		preg_match_all('/<title>(.*?)<\/title>/i', $content, $title);
		preg_match_all('/<meta name="description" content="(.*?)"/i', $content, $description);
		preg_match_all('/<meta name="keywords" content="(.*?)"/i', $content, $key);

		return array(
			'title' => isset($title[1][0]) ? $title[1][0] : '',
			'description' => isset($description[1][0]) ? $description[1][0] : '',
			'key' => isset($key[1][0]) ? $key[1][0] : ''
		);
	}
 
 	//Перевод текста с кириллицы в траскрипт
	protected function trans($str) {
    	$str = preg_replace('/[^0-9a-zA-Zа-яА-Я]/ui', ' ', $str);
    	$tr = array(
        "А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
        "Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
        "Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
        "О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
        "У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
        "Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
        "Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
        "в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
        "з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
        "м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
        "с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
        "ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
        "ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
		" " => "-","ё" => "e" 
    	);

    	$str = urlencode(strtolower(strtr(trim($str),$tr)));
    	$str =  preg_replace('/[-]+/', '-', $str); 
    	return $str;
	}
}