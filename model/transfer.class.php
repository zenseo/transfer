<?php

class Transfer {

	var $config = ''; 

	function __construct($config) {
		global $modx;

        $this->config = $config;
        $this->modx = $modx;
   	}

   	function update_content_from_document() {

   		global $modx;

		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');

		$docs = $this->get_content("parent = 4");


 		
 		foreach($docs as $doc) {

 			echo  $doc['id'].'<br />';

 			$document = explode('<ul>',$doc['content']);
 			 
 			$html = str_get_html($document[0]);

 			$json = [];

				foreach ($html->find('a') as $k => $element) { 
					//echo  $k.')'.$element->href."->".$element->title;
					//echo '<br />';

					$title = strlen($element->title) > 1 ? $element->title : "";

					$json[] = array($element->href,"", $title);
				}
			
 			 echo $json  = json_encode($json);
 			//$this->set_tv($tmplvarid = 7, intval($doc['id']), $json);
 			echo '<hr />';
 			/*


 			$content = explode("руб.</p>",$doc['content']);
 			$content = explode('<h2 class="tab_info">',$content[1]);

			$i = explode("Планировка дома",$doc['content']);

			$i = str_get_html($i[1]);

			$json = array();

			foreach ($i->find('a') as $k => $element) { 
				$json[] = array($element->href,"","");
			}

			echo $json  = json_encode($json);

 			echo '<br />';	  
			echo  $doc['id'];
 					echo '<br />';
 			echo $content = $content[0];
 			
			echo '<br />';
 			echo $image = str_replace('../..','',$html->find('img',0)->src);
 			echo '<hr />';
 
 			$this->set_tv($tmplvarid = 8, $doc['id'], $image);
 			$this->set_tv($tmplvarid = 7, $doc['id'], $json);
 			$this->set_tv($tmplvarid = 9, $doc['id'], $content);
 			*/
 		}
	} 

   	function update_content_from_chunk() {

   		global $modx;

		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');

		$html = $modx->getChunk('slider_main');
 
		$html = str_get_html($html);

		foreach($html->find('#portfolio li') as $k => $element) {
			$c = str_get_html($element->innertext);

			echo $src = $c->find('img',0)->src  ;
			echo '<br /> url>';
			echo $url = $c->find('a',0)->href  ;
		 
			 $t = $c->find('.slideinfotext',0)->innertext  ; 
			echo '<br />';

			$t = explode('площадь ', $t);
			$sq = explode(' ', $t[1]);
			echo $sq = $sq[0];
echo '<br />';
			$t = explode('цена ', $t[1]);
			$price = explode(' руб.', $t[1]);
			echo $price = $price[0];
 
			$x = $this->get_content("pagetitle  LIKE \"%".trim($sq)."%\" and parent = 20");

			if($x[0]['id'] > 0) {
				echo '<br />>';
				echo $x[0]['id'];
				echo '<hr />';
				
				//$this->set_tv($tmplvarid = 4, $x[0]['id'], $src);
				//$this->set_tv($tmplvarid = 6, $x[0]['id'],$sq);
				 //$this->set_tv($tmplvarid = 5, $x[0]['id'], $price);
				 
			}
  	 
  	
		}

	} 

   	function set_alias() {

   		$urls = $this->get_content(' parent = 5 ', 1000);

		foreach ($urls as $value) {
   			
   			$fields = array( 
				'alias' => $this->trans($value['pagetitle']) 
			);

			$this->update_content($fields, $value['id']);

		}

   	}

   	function set_page() { 
   		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');

   		$urls = array(
   			'http://www.stroy-b.ru/uslugi.html', 
   		);

   		foreach ($urls as $url) {
   			$html = $this->file_get_contents_curl($url);
			//$html = iconv('cp1251', 'utf-8', $html); //uf8 on convert
 

			$html = str_get_html($html); 

			foreach($html->find('.menu') as $element) {
				echo $element->href . '->'.$element->innertext.'<br>';
  			 /*
				$this->set_content(
					$pagetitle = $element->innertext, 
					$longtitle = '', 
					$alias = $this->trans($element->innertext), 
					$parent = 4, 
					$template = 3, 
					$content = '', 
					$description = $element->href
				); 
			 */
			 
			}
   		}
 
   	}

   	function set_parser_content() {

		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');
 
		$urls = $this->get_content(' longtitle = "" and description > "" ', 10);

		foreach ($urls as $value) {

			$value['description'] = trim($value['description']);
			$get_url = '';

			if(!empty($value['description'])) {

				if(strpos($value['description'], $this->config['domen'])) {
					$get_url = $value['description'];
				} else if(strcmp('/', substr($value['description'],0,1)) == 0) {
					$get_url = 'http://'.$this->config['domen'].''.$value['description'];
				} else {
					$get_url = 'http://'.$this->config['domen'].'/'.$value['description'];
				}

				echo $get_url; 

				$html = $this->file_get_contents_curl($get_url);
				//$html = iconv('cp1251', 'utf-8', $html);
				
				$html = str_get_html($html); 
 
				if($html) {

					$h1 = $html->find('h1', 0)->innertext;  

					$content = $this->filter_content(
						$html->find('#content', 0)->innertext
					);
					 
					$meta = $this->get_meta( $html->find('head', 0)->innertext);

					//$price = @$html->find('.red', 0)->innertext;
 
					echo $this->report_parser_content(
						array(
							'h1' => $h1,
							'content' => $content,
							'meta' => $meta,
							'tv' => array(
								//'price' => $price
							)
						)
					);

					$set_content = 1;

					if($set_content > 0) {

						$this->set_tv($tmplvarid = 2, $value['id'], $meta['title']);
						$this->set_tv($tmplvarid = 3, $value['id'], $meta['description']);
						$this->set_tv($tmplvarid = 1, $value['id'],  $meta['key']);
 
 						//$this->set_tv($tmplvarid = 4, $value['id'], $price);

						//обновляем документ
						$fields = array(
							'content' => $content,
							'longtitle' => $h1 
						);

						$this->update_content($fields, $value['id']);

					} 
 
					echo '<p style="color:green;">'.$get_url.'<hr /> </p>';

				} else {
					echo '<p style="color:red;">'.$get_url.'<hr /> </p>';
				}
				 
			} else {
					echo '<p style="color: blue;">';
					echo $value['pagetitle'];
					echo '<hr />';	
					echo '</p>';
			}
			//break;
		}
	} 

	function filter_content($content = '') {

		$content = preg_replace('~<h1>(.*?)</h1>~i','',$content);
		$content = preg_replace('~<div id="button_cena_na_uslugu">(.*?)</div>~i','',$content);

		$content = explode('<a name="mailform">', $content);

		return $content[0];
	}

	function report_parser_content($c = array()) {
		$out = '';

		if(empty($c['h1'])) {
			$out .=  '<p style="color:red"><b>h1:</b>  </p>';
		} else {
			$out .=  '<p style="color:green"><b>h1:</b>  '.$c['h1'].'</p>';
		}

		if(empty($c['meta']['title'])) {
			$out .=  '<p style="color:red"><b>title:</b>  </p>';
		} else {
			$out .=  '<p style="color:green"><b>title:</b>  '.$c['meta']['title'].'</p>';
		}

		if(empty($c['meta']['description'])) {
			$out .=  '<p style="color:red"><b>description:</b>  </p>';
		} else {
			$out .=  '<p style="color:green"><b>description:</b>  '.$c['meta']['description'].'</p>';
		}

		if(empty($c['meta']['key'])) {
			$out .=  '<p style="color:red"><b>key:</b>  </p>';
		} else {
			$out .=  '<p style="color:green"><b>key:</b> '.$c['meta']['key'].'</p>';
		}

		if(empty($c['content'])) {
			$out .=  '<p style="color:red"><b>content:</b> </p>';
		} else {
			$out .=  '<p style="color:green"><b>content:</b> '.'</p>'; //$c['content']
		}

		//custom value

		if(empty($c['tv']['price'])) {
			//$out .=  '<p style="color:red"><b>tv.price:</b></h1> </p>';
		} else {
			//$out .=  '<p style="color:green"><b>tv.price:</b></h1> '.$c['tv']['price'].'</p>';
		}

		return $out;
	}
 
	function set_redirect_url() {
		global $modx; 
		$urls = $this->get_content();
 
		foreach ($urls as $key => $value) {
			$value['description'] = trim($value['description']);

			if(!empty($value['description'])) { 
				echo '<p style="color: black;">';

				echo ' RewriteRule '.
				 
					preg_replace('/[\.\/]+/i','\\\\\0',$value['description'])
				 
				.' '. 
				$modx->makeUrl(intval($value['id']), '', '', 'full').' [R=301,L]';

				echo '</p>';
			}
		}
		 
	} 

	private function set_url_rewrite() {

	}

	private function set_content(
				$pagetitle, 
				$longtitle, 
				$alias, 
				$parent, 
				$template, 
				$content,
				$description
	) {
		$out = 0;
		if($this->check_isset_content($alias)) { 
		
		} else {
			
			$id_insert = $this->insert_content(
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

	function get_content($where = '', $limit = '', $active = 1){
		global $modx;

		$where_active = '   published = 1 AND deleted = 0 AND id > 1';

		if($active > 0) {
			if(empty($where)) {
				$where = $where_active;
			} else {
				$where = $where.' AND '.$where_active; 
			}
		}

		$limit = empty($limit) ? ' LIMIT 1000' : ' LIMIT '.$limit;
		 
		$out = array();
		$get_content = $modx->db->query(' SELECT * FROM modx_site_content WHERE '.$where.$limit);

		while($row = $modx->db->getRow($get_content)){ 
		 	$out[] = $row;
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

	function check_isset_content($alias = '') {
		global $modx;
		return  $modx->db->getValue(
 					$modx->db->select(
 						"id",
 						$modx->getFullTableName("site_content"),
 						 "alias = '".$alias."'"
 			)
		);
	}

	function update_content($fields, $where) {
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

	function insert_content(
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
		 curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);

		//имитируем браузер опера
		 curl_setopt($ch, CURLOPT_USERAGENT, 'Opera/9.80 (Windows NT 5.1; U; ru) Presto/2.7.62 Version/11.01');

		curl_setopt($ch, CURLOPT_URL, $url);

		$data = curl_exec($ch);
		curl_close($ch);

		return $data;
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