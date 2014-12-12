<?php
	ini_set('xdebug.var_display_max_depth', 500);
	ini_set('xdebug.var_display_max_children', 2048);
	ini_set('xdebug.var_display_max_data', 28186);
	require_once(EXTENSIONS.'\twitter_feed\class\craur\src\Craur.php');

	
	Class Tweets{		
		
		public $start = 0;
		
		public function init($newconfig){
			require_once EXTENSIONS . '\twitter_feed\class\config.php';			
			$config = array_merge($config,$newconfig);			
			$tweets = $this->processing($config);
			
			/*
			gotta check for possible feature to only show retweets or show tweets from own specified widgetid twitter account
			*/
			$new_tweets = array();
			if($config['searched_user'] != ''){	
				
				$new_tweets = $this->search($tweets,$config);
			}else{
				
				$new_tweets = $tweets;
			}
			
			$xml = array();
			$xml[] = '<ul class="tweets">';
			$d = '';
			$author = '';
			$timelink = '';
			$start = 0;
			foreach($new_tweets as $c=>$contains){		
				if($start < $config['amount_of_tweets']){
					$divs = array_values((array)$contains);
					
						$xml[] = '<li>';
						$inner = $contains->get('div[]');			
						$d =array();
						foreach($inner as $divs => $div){	
								$converted = array_values((array) $div);
								
										$d['author'][] = $this->header($converted,$div,$config);//($this->header($converted,$div,$config))? $this->header($converted,$div,$config): null ;
										$d['contents'][] = $this->content($converted,$div,$config);//($this->content($converted,$div,$config))? $this->content($converted,$div,$config): null ;
										$d['tweet-actions'][] = $this->footer($converted,$div,$config); //($this->footer($converted,$div,$config))? $this->footer($converted,$div,$config): null ;
								
						}					
						$d['author'] = implode($d['author'],'');
						$d['contents'] = implode($d['contents'],'');
						$d['tweet-actions'] = implode($d['tweet-actions'],'');
						
						$ordered = $this->sortArrayByArray($d,$config['order']);					
						$xml[] = implode($ordered,'');			
						$xml[] ='</li>';
					
										
				}
				$start++;
			}
			$xml[] = '</ul>';
			return implode($xml,'');
		}
		public function search($tweets,$config){
			$new_tweets = array();
			if($tweets){
				foreach($tweets as $tweet=>$tw){
						$con = array_values((array)$tw);					
						if(array_key_exists('@',$con[0]['div'][0]['div']['a']['span'][0]['span'])){
							if($con[0]['div'][0]['div']['a']['span'][0]['span']['@'] == $config['searched_user'] || $con[0]['div'][0]['div']['a']['span'][1]['b'] == $config['searched_user']){
								$new_tweets[$tweet] = $tw; 
							}
						}elseif(array_key_exists(0,$con[0]['div'][0]['div']['a']['span'][0]['span'])){
							
							if($con[0]['div'][0]['div']['a']['span'][0]['span'][0]['@'] == $config['searched_user'] || $con[0]['div'][0]['div']['a']['span'][1]['b'] == $config['searched_user']){
								$new_tweets[$tweet] = $tw; 
							}
						}
						
				}
				return $new_tweets;
			}else{
				return false;
			}			
		}
		public  function sortArrayByArray(Array $array, Array $orderArray) {
			$ordered = array();
			foreach($orderArray as $key) {
				if(array_key_exists($key,$array)) {
					$ordered[$key] = $array[$key];
					unset($array[$key]);
				}
			}
			return $ordered + $array;
		}
		public function write($json){
			file_put_contents(EXTENSIONS . '/twitter_feed/config.json',$json);
			return true;
		}
		public function processing($config){
			
			$url = $config['url']. $config['widgetId'];
			$this->deleteOldFile(EXTENSIONS . '/twitter_feed/config.json');
			if(file_exists(EXTENSIONS . '/twitter_feed/config.json')){
				$tweets = file_get_contents(EXTENSIONS . '/twitter_feed/config.json');
			}else{
				$tweets = file_get_contents($url);
				$this->write($tweets);
			}
			
			$tweets = json_decode($tweets);
			$tweets = $tweets->body;
			$craur_node = Craur::createFromHTML($tweets);
			$container = $craur_node->get('div')->get('div[]');	
			$tweets = $container[2];
			$tweets = $tweets->get('ol')->get('li[]');
			
			return $tweets;			
		}	
		public function deleteOldFile($object) {
			if (filemtime($object) < time() - 600) {
			  // Remove empty directories...
			  if (is_dir($object)) rmdir($object);
			  // Or delete files...
			  else unlink($object);
			}
		}
		
		public function header($converted,$div,$config){
			$d = array();
			$author = '';
			$timelink = '';
			if(array_key_exists('@class',$converted[0]) && $converted[0]['@class'] == 'header'){
						$d[] = '<div class="section author">';															
						if($config['avatar_required'] == true){
								$screenshot = array_values((array) $div->get('div.a'));								
								if(array_key_exists('img',$screenshot[0]) && array_key_exists('@src',$screenshot[0]['img'])){
									$author .= '<a class="'.$div->get('div.a.@class').' avatar" href="'.$div->get('div.a.@href').'">';
									$img = '<img class="'.$screenshot[0]['img']['@class'].'" src="'.$screenshot[0]['img']['@src'].'"/>';
									$author.= $img;
									$author.= '</a>';
								}
								if(array_key_exists('span',$converted[0]['div']['a'])){
									$author .= '<a class="'.$div->get('div.a.@class').' screenname" href="'.$div->get('div.a.@href').'">';
									$author .= '<span class="'.$div->get('div.a.span.@class').'">';
									$screenname = $div->get('div.a.span[]');
									if($config['screenname_only'] === true){
										$screenname = $screenname[1]->get('@') . $screenname[1]->get('b');
									}else{
										$screenname = $screenname[0]->get('span')->get('@');
									}
									$author .= $screenname;								
									$author .= '</span>';
									$author .= '</a>';
								}
						}else{
								if(array_key_exists('span',$converted[0]['div']['a'])){									
									$author .= '<a class="'.$div->get('div.a.@class').' screenname" href="'.$div->get('div.a.@href').'">';
									$author .= '<span class="'.$div->get('div.a.span.@class').'">';
									$screenname = $div->get('div.a.span[]');
									if($config['screenname_only'] === true){
										$name = $screenname[1]->get('@') . $screenname[1]->get('b');
									}else{
										$name = $screenname[0]->get('span')->get('@');
									}
									$author .= $name;								
									$author .= '</span>';
									$author .= '</a>';
								}							
						}						
						$d[]= $author;
						$author = '';
						$d[]= '</div>';													
						$d[]= '<div class="section time">';									
						if($config['time_required'] == true && $config['time_labels'] == true){
								$timelink .= '<a class="'.$div->get('a.@class').'" href="'.$div->get('a.@href').'">';
								$timelink.= $div->get('a.time.@aria-label');
								$timelink.= '</a>';								
						}elseif($config['time_required'] == false){
						
								$timelink .= '';
						
						}else{
								$timelink .= '<a class="'.$div->get('a.@class').'" href="'.$div->get('a.@href').'">';
								if(array_key_exists('abbr',$converted[0]['a']['time'])){
									$timelink.= $div->get('a.time.@') .' '.$div->get('a.time.abbr.@');
								}else{
									$timelink.= $div->get('a.time.@');
								}								
								$timelink.= '</a>';								
						}
						$d[] = $timelink;
						$timelink = '';
						$d[] = '</div>';	
				}
				return implode($d,'');
		}
		
		public function content($converted,$div,$config){
				$timelink = '';
				$d = array();
				if(array_key_exists('@class',$converted[0]) && $converted[0]['@class'] == 'e-entry-content'){
						$d[]= '<div class="section contents">';								
						$d[]= $div->get('p')->toXmlString();
						$d[]= $timelink;
						$timelink = '';
						$d[]= '</div>';	
				}			
				return implode($d,'');
		}
		public function footer($converted,$div,$config){
			$d = array();
			if(array_key_exists('@class',$converted[0]) && $converted[0]['@class'] == 'footer customisable-border'){
						$d[]= '<div class="section tweet-actions">';
						$tweetactions = '';
						if($config['show_links'] == true){						
							if($config['stats_required'] == true && array_key_exists('span',$converted[0])){								
								$tweetactions .= $div->get('span')->toXmlString();
								$tweetactions .= '<ul class="'.$div->get('ul.@class').'">';
								$tweetactions .= $div->get('ul')->toXmlString();
								$tweetactions .= '</ul>';												
								$d[] = $tweetactions;								
							}else{
								$tweetactions .= '<ul class="'.$div->get('ul.@class').'">';
								$tweetactions .= $div->get('ul')->toXmlString();
								$tweetactions .= '</ul>';	
								$d[] = $tweetactions;
							}
						}	
						$tweetactions = '';
						$d[]= '</div>';						
			}
			return implode($d,'');
		}
	}

?>