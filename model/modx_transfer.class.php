<?php

class Modx_transfer {

	var $config = ''; 

	function __construct($config) {
		global $modx;

        $this->config = $config;
        $this->modx = $modx;
   	}

   	function set_parser_content() {
		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');
 
		$urls = $this->get_site_links();

		foreach ($urls as $key => $value) {

			$value['link_attributes'] = trim($value['link_attributes']);

			if(!empty($value['link_attributes'])) {

				$html = file_get_html($this->config['domen'].'/'.$value['link_attributes']);

				if($html) {

					$h1 = $html->find('h1', 0)->innertext; 

					$content = $html->find('#main_cont', 0)->innertext;

					$meta = $this->get_meta($html->find('head', 0)->innertext);
					$this->set_tv($tmplvarid = 1, $value['id'], $this->config['tv_title']);
					$this->set_tv($tmplvarid = 2, $value['id'], $this->config['tv_description']);
					$this->set_tv($tmplvarid = 3, $value['id'], $this->config['tv_key']);
 
					//обновляем документ
					$fields = array(
						'content' => $content
					);

					$this->update_content($fields, $value['id']);

					//заносим мета теги
 
					echo '<p style="color:green;">';
					print_r($value);
					echo '<hr />';
					echo '</p>';

				} else {
					echo '<p style="color: red;">';
					print_r($value);
					echo '<hr />';	
					echo '</p>';
				}
				 
			} else {
					echo '<p style="color: blue;">';
					print_r($value);
					echo '<hr />';	
					echo '</p>';
			}
		}
	} 
 
	function set_redirect_url() {
		global $modx;
		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');

		$urls = $this->get_site_links();

		foreach ($urls as $key => $value) {
			$value['link_attributes'] = trim($value['link_attributes']);
			if(!empty($value['link_attributes'])) { 
				echo '<p style="color: black;">';

				echo ' RewriteRule '.$this->config['domen'].
				 
					preg_replace('/[\.\/]+/i','\\\\\0',$value['link_attributes'])
				 
				.' '. 
				$modx->makeUrl(intval($value['id']), '', '', 'full').' [R=301,L]';

				echo '</p>';
			}
		}
	} 

	private function set_url_rewrite() {

	}

	private function set_content() {
		$out = 0;
		if($this->check_isset_content($value['url'])) { 
		
		} else {
			
			$id_insert = $this->insert_content(
				$pagetitle = '', 
				$longtitle = '', 
				$alias = '', 
				$parent = '', 
				$template = 1, 
				$content = ''
			);
		}
		return $out;
	}

	private function set_tv($tmplvarid, $contentid, $value) { 
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

	function check_isset_content($alias) {
		global $modx;
		return  $modx->db->getValue(
 					$modx->db->select(
 						"id",
 						$modx->getFullTableName("site_content"),
 						 "alias = '".$alias."'"
 			)
		);
	}

	function update_content($fields, $id) {
		global $modx;

		$set_field = array();

		foreach ($fields as $key => $value) {
			$set_field[] = $key." = '".$modx->db->escape($value)."'";
		}

		$set_field = implode(', ',$set_field);

		return $modx->db->update(
			$set_field, 
			$modx->getFullTableName("site_content"), 
			" id = ".intval($id)." "
		);
	}

	function insert_content(
		$pagetitle = '', 
		$longtitle = '', 
		$alias = '', 
		$parent = '', 
		$template = 1, 
		$content = ''
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
					'hidemenu' => 0
				), $modx->getFullTableName('site_content'))
		) {
			return $modx->db->getInsertId();
		} else {
			return false;
		}

	}

	function check_insert_tv($contentid, $tmplvarid) {
		global $modx;
		return  $modx->db->getValue(
 					$modx->db->select(
 						"id",
 						$modx->getFullTableName("site_tmplvar_contentvalues"),
 						 "tmplvarid = ".$tmplvarid." and value > '' AND contentid = ".$contentid
 			)
		);
	}

	function insert_tv($tmplvarid, $contentid, $value) {
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

	function update_tv($tmplvarid, $contentid, $value) {
		global $modx;

		return $modx->db->update(
				"value = '".$modx->db->escape($value)."'", 
			$modx->getFullTableName("site_tmplvar_contentvalues"), 
			"`tmplvarid` = ".intval($tmplvarid)." AND
			 `contentid` = ".intval($contentid)
		);
	}

	function file_get_contents_curl($url) {
		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		 //Устанавливаем параметр, чтобы curl возвращал данные, вместо того, чтобы выводить их в браузер.
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//разрешаем перенаправление на полученный в заголовке URL
		//curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);

		//имитируем браузер опера
		 //curl_setopt($curl, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');

		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
	}

	function get_site_links($where = ' published = 1 AND deleted = 0 AND id > 1 '){
		global $modx;
		 
		$out = array();
		$get_content = $modx->db->query(' SELECT * FROM modx_site_content WHERE '.$where);

		while($row = $modx->db->getRow($get_content)){
		 	$url = $modx->makeUrl(intval($row['id']), '', '', 'full');

		 	$out[] = array(
		 		'url' => $url,
		 		'id' => $row['id'],
		 		'link_attributes' => $row['link_attributes'],
		 		'alias' => $row['alias'],
		 	);
		}

		return $out;
	}

	function get_meta($content) { 

		preg_match_all('/<title>(.*?)<\/title>/i', $content, $title);
		preg_match_all('/<meta name="description" content="(.*?)"/i', $content, $description);
		preg_match_all('/<meta name="keywords" content="(.*?)"/i', $content, $key);

		return array(
			'title' => $title[1][0],
			'description' => $description[1][0],
			'key' => $key[1][0]
		);
	}
 
 // функция превода текста с кириллицы в траскрипт
	public function trans($str) {
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