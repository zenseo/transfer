<?php

class Transfer extends Api {

	var $config = ''; 

	function __construct($config) {
		global $modx;

        $this->config = $config;
        $this->modx = $modx;
   	} 

   	//создание структуры сайта из файла
 
   	public function set_tree_site(
   		$url_file = 'https://dl.dropboxusercontent.com/u/18352137/temp/sedlog.txt',
   		$template = 3
   	) {
   		$tree = array();

   		if(!empty($url_file)) {
   			$tree = file($url_file);

   			if(!empty($tree) && is_array($tree)) {
   				foreach ($tree as $key => $value) { 
   					$tree_item = explode($this->config['domen'].'/',$value);
   					$tree_item = $tree_item[1];
   					if(!empty($tree_item)) {

   						//очистка от лишних элементов
   						$tree_item = explode('/',$tree_item);

   						$item = array();
   						foreach ($tree_item as $key => $value) {
   							$value = trim($value);
   							if(!empty($value)) { 
   								$item[] = $value;
   							}
   						}
   						//внесение
   						$parent = 0;

   						foreach ($item as $key => $value) {
   							$parent = $this->set_content(
								$value, //pagetitle
								'',  //longtitle
								$value, //alias
								$parent, 
								$template, 
								'', //content
								'' //description
							);
   						}

   						print_r($item);

   						echo '<hr />';
   					}
   				}
   			}
   		}
   	}

	// обновление документа из чанка
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

   	//Создание страниц из спарсенных ссылок
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

   	//перенос контента со старых страниц на новые
   	function set_parser_content(
   		$where_document = ' longtitle = "" ', //для каких документов переносить
   		$limit_document = 30, //число страниц для переноса за раз
   		$field_name = 'alias', //поле, где хранится старый адрес
   		$set_content = 1 //занесение контента
   	) {

		include(PATH_MODXTRANSFER.'model/simple_html_dom.php');
 
		$urls = $this->get_content($where_document, $limit_document);
 
		foreach ($urls as $value) {

			$value[$field_name] = trim($value[$field_name]);
			$get_url = '';

			if(!empty($value[$field_name])) {
 
				$get_url = $this->_get_normal_url('', $value['id']); 

				$html = $this->file_get_contents_curl($get_url);
				//$html = iconv('cp1251', 'utf-8', $html);
				$html = str_get_html($html); 
 				
 				$this->set_report_status($get_url, 0);
 				 
				if($html) {

					$h1 = $html->find('h1', 0)->innertext;  

					$content = $this->_filter_content(
						$html->find('td.content', 0)->innertext
					);
					 
					$meta = $this->get_meta( $html->find('head', 0)->innertext);

					//$price = @$html->find('.red', 0)->innertext;
 
					echo $this->_report_parser_content(
						array(
							'h1' => $h1,
							'content' => $content,
							'meta' => $meta,
							'tv' => array(
								//'price' => $price
							)
						)
					);
 
					if(1 > 0) {

						$this->set_tv($tmplvarid = 3, $value['id'], $meta['title']);
						$this->set_tv($tmplvarid = 1, $value['id'], $meta['description']);
						$this->set_tv($tmplvarid = 2, $value['id'],  $meta['key']);
 
 						//$this->set_tv($tmplvarid = 4, $value['id'], $price);

						//обновляем документ
						$fields = array(
							'content' => $content,
							'longtitle' => $h1,
							'pagetitle' => $h1
						);

						$this->update_content($fields, ' id = '.$value['id']);

					} 
				}  
			 
			} else { 
				$this->set_report_status($value['pagetitle'], 0);
			} 
		}
	}
 
	private function set_report_status($value = '', $type = 0) {
		$out = '';

		switch ($type) {
    		case 0: //neutral
        		 $status = 'blue';
        	break;
    		case 1: //error
        		 $status = 'red';
        	break;
    		case 2: //success
        		 $status = 'green';
        	break;
		}
 
		echo '<p style="color: '.$status.';">'.$value.'<hr /></p>';
	}

	//приведение ссылки в должный вид
	private function _get_normal_url($url, $id) {
		global $modx;

		$get_url = '';

		if(empty($id)) {
			if(strpos($url, $this->config['domen'])) {
				$get_url = $value[$field_name];
			} else if(strcmp('/', substr($url,0,1)) == 0) {
				$get_url = 'http://'.$this->config['domen'].''.$url;
			} else {
				$get_url = 'http://'.$this->config['domen'].'/'.$url;
			}
		} else {
			$get_url = $modx->makeUrl(intval($id), '', '', 'full');
			$get_url = str_replace('test.', 'www.', $get_url);
		}

 		return $get_url;
	}

	//очистка контента для переноса контента
	private function _filter_content($content = '') {

		$content = explode('</h1>',$content);
 
		return $content[1];
	}

	//вывод отчёта переноса контента
	private function _report_parser_content($c = array()) {
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
 	
 	//генерация редиректов
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

	//генерация чпу для заданных страниц
   	function set_alias() {

   		$urls = $this->get_content(' parent = 5 ', 1000);

		foreach ($urls as $value) {
   			
   			$fields = array( 
				'alias' => $this->trans($value['pagetitle']) 
			);

			$this->update_content($fields, $value['id']);

		}
   	}
}