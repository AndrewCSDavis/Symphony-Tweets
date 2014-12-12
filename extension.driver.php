<?php

	class extension_twitter_feed extends Extension {
		
		public function getSubscribedDelegates(){
			return array(
				array(
					'page' => '/backend/',
					'delegate' => 'InitaliseAdminPageHead',
					'callback' => 'appendAssets'
					
				),				
				array(
					'page' => '/frontend/',
					'delegate' => 'FrontendOutputPreGenerate',
					'callback' => 'registerFunctions'
				),	
				
				array(
					'page'=> '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'addPreferences'
				),
				array(
					'page'=> '/system/preferences/',
					'delegate' => 'save',
					'callback' => 'savePreferences'
				)
			);
		}

		public function registerFunctions($context){
			
			$context['page']->registerPHPFunction('gettweets');			
		}
		
		
		
		public function addpreferences(array $context){
			require_once EXTENSIONS . '\twitter_feed\class\config.php';
			$fieldset = new XMLElement('fieldset');
			$fieldset->setAttribute('class', 'settings');
			$fieldset->appendChild(new XMLElement('legend', __('Twitter Feed')));
			$fieldset->appendChild(
				new XMLElement('p', __('Provide your Twitter requirements'), array('class' => 'help'))
			);

			$div = new XMLElement('div');			
			$label = new XMLElement('label', __('Widget ID'));
			$label2 = new XMLElement('label', __('Amount of tweets'));
			$label3 = new XMLElement('label', __('Show Action Links (Reply, Retweet, Favourite)'));
			$label4 = new XMLElement('label', __('Show Screenname only'));
			$label5 = new XMLElement('label', __('Show Users Avatar'));
			$label6 = new XMLElement('label', __('Do you require the time to be present ? (if not ignore next option (format time)) '));
			$label7 = new XMLElement('label', __('Change format of the time label used '));
			$label8 = new XMLElement('label', __('Do you require the Stats of each tweet ? (displays the amount of retweets and favourites it has received) '));
			$label9 = new XMLElement('label', __('Rearrange the order of the contents how you would like them displayed'));
			$label10 = new XMLElement('label', __('type in and save to create a customised user timeline (requires username or screenname) <i>(accepts only exact)</i>'));
			$label11 = new XMLElement('label');

			
			if(Symphony::Configuration()->get('widgetId','twitter-feed')){
				$wid = Symphony::Configuration()->get('widgetId','twitter-feed');				
			}else{
				$wid = $config['widgetId'];
			}
			
			if(Symphony::Configuration()->get('screenname_only','twitter-feed')){
				$screennameonly = Symphony::Configuration()->get('screenname_only','twitter-feed');				
			}else{
				$screennameonly = $config['screenname_only'];
			}
			
			if(Symphony::Configuration()->get('show_links','twitter-feed')){
				$showlinks = Symphony::Configuration()->get('show_links','twitter-feed');				
			}else{
				$showlinks = $config['show_links'];
			}
			
			if(Symphony::Configuration()->get('avatar_required','twitter-feed')){
				$avatar = Symphony::Configuration()->get('avatar_required','twitter-feed');				
			}else{
				$avatar = $config['avatar_required'];
			}
			
			if(Symphony::Configuration()->get('time_labels','twitter-feed')){
				$timelabels = Symphony::Configuration()->get('time_labels','twitter-feed');				
			}else{
				$timelabels = $config['time_labels'];
			}
			
			if(Symphony::Configuration()->get('time_required','twitter-feed')){
				$timerequired = Symphony::Configuration()->get('time_required','twitter-feed');				
			}else{
				$timerequired = $config['time_required'];
			}
			
			if(Symphony::Configuration()->get('amount_of_tweets','twitter-feed')){
				$tweetamount = Symphony::Configuration()->get('amount_of_tweets','twitter-feed');
			}else{
				$tweetamount = $config['amount_of_tweets'];
			}
			
			if(Symphony::Configuration()->get('stats_required','twitter-feed')){
				$statsrequired = Symphony::Configuration()->get('stats_required','twitter-feed');
			}else{
				$statsrequired = $config['stats_required'];
			}
			if(Symphony::Configuration()->get('order','twitter-feed')){
				$order = explode(',',Symphony::Configuration()->get('order','twitter-feed'));
			}else{
				$order = $config['order'];
			}
			
			if(Symphony::Configuration()->get('searched_user','twitter-feed')){
				$searcheduser = Symphony::Configuration()->get('searched_user','twitter-feed');
			}else{
				$searcheduser = '';//$config['searched_user'];
			}
					
			$widfield = Widget::Input('settings[twitter-feed][widgetId]',$wid,'text');
			$searcheduserfield = Widget::Input('settings[twitter-feed][searched_user]',$searcheduser,'text');
			$orderinput = Widget::Input('settings[twitter-feed][order]',implode($order,','),'hidden');
			
			$orderfield = new XMLElement('select');						
			$orderfield->setAttribute('multiple','multiple');
			$orderfield->setAttribute('id','order-contents');
			$optionsarray = array('author','contents','tweet-actions');
			foreach($order as $status=> $orderval){
				$options = new XMLElement('option',$orderval);
				$options->setAttribute('value',$orderval);				
				if(array_key_exists($orderval,$optionsarray)){
					$options->setAttribute('selected','selected');				
				}
				$orderfield->appendChild($options);
			}			
			
			$avatarfield = new XMLElement('select');			
			$avatarfield->setAttribute('name','settings[twitter-feed][avatar_required]');
			
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);
				if($status == $avatar){
					$options->setAttribute('selected','selected');
				}
				$avatarfield->appendChild($options);
			}
			
			
			$statsrequiredfield = new XMLElement('select');			
			$statsrequiredfield->setAttribute('name','settings[twitter-feed][stats_required]');
			
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);
				if($status == $statsrequired){
					$options->setAttribute('selected','selected');
				}
				$statsrequiredfield->appendChild($options);
			}
			
			$timelabelsfield = new XMLElement('select');			
			$timelabelsfield->setAttribute('name','settings[twitter-feed][time_labels]');
			
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);
				if($status == $timelabels){
					$options->setAttribute('selected','selected');
				}
				$timelabelsfield->appendChild($options);
			}
			
			$timerequiredfield = new XMLElement('select');			
			$timerequiredfield->setAttribute('name','settings[twitter-feed][time_required]');
			
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);
				if($status == $timerequired){
					$options->setAttribute('selected','selected');
				}
				$timerequiredfield->appendChild($options);
			}
			
			
			$linksfield = new XMLElement('select');
			$linksfield->setAttribute('name','settings[twitter-feed][show_links]');
			
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);				
				if($showlinks == $status){
					$options->setAttribute('selected','selected');
				}
				$linksfield->appendChild($options);
			}
			
			$screennamefield = new XMLElement('select');
			$screennamefield->setAttribute('name','settings[twitter-feed][screenname_only]');
			foreach(array('true','false') as $status){
				$options = new XMLElement('option',$status);
				$options->setAttribute('value',$status);
				if($status == $screennameonly){
					$options->setAttribute('selected','selected');
				}
				$screennamefield->appendChild($options);
			}
			
			$tweetsfield = new XMLElement('select');
			$tweetsfield->setAttribute('name','settings[twitter-feed][amount_of_tweets]');
			for($i = 1; $i < 21; $i++ ){
				$opt = new XMLElement('option',$i);	
				$opt->setAttribute('value',$i);
				if($tweetamount == $i){
					$opt->setAttribute('selected','selected');
				}
				$tweetsfield->appendChild($opt);				
			}			
			
			
			$label->appendChild($widfield);
			$label2->appendChild($tweetsfield);			
			$label3->appendChild($linksfield);
			$label4->appendChild($screennamefield);
			$label5->appendChild($avatarfield);
			$label6->appendChild($timerequiredfield);
			$label7->appendChild($timelabelsfield);
			$label8->appendChild($statsrequiredfield);			
			$label9->appendChild($orderinput);
			$label9->appendChild($orderfield);
			$label10->appendChild($searcheduserfield);
			$label11->setValue(" use <strong>'php:function('gettweets','')'</strong> in a value-of select & disable output escaping <strong>(also needs php namespacing)</strong>");
			$div->appendChild($label10);
			$div->appendChild($label);
			$div->appendChild($label2);
			$div->appendChild($label3);
			$div->appendChild($label4);
			$div->appendChild($label5);
			$div->appendChild($label6);
			$div->appendChild($label7);
			$div->appendChild($label8);
			$div->appendChild($label9);
			$div->appendChild($label11);
			$fieldset->appendChild($div);

			$context['wrapper']->appendChild($fieldset);
		}
		
		public function appendAssets(array $context) {
						// store de callback array localy
			$c = Administration::instance()->getPageCallback();
			
			// extension page
			if($c['driver'] == 'systempreferences') {

				Administration::instance()->Page->addStylesheetToHead(
					URL . '/extensions/twitter_feed/assets/selectize.css',
					'screen',
					time() + 1,
					false
				);
				Administration::instance()->Page->addStylesheetToHead(
					URL . '/extensions/twitter_feed/assets/jquery-ui.css',
					'screen',
					time() + 1,
					false
				);
				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/twitter_feed/assets/jquery-ui.min.js',
					time(),
					false
				);
				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/twitter_feed/assets/jquery.selectize.js',
					time(),
					false
				);
				Administration::instance()->Page->addScriptToHead(
					URL . '/extensions/twitter_feed/assets/init.js',
					time(),
					false
				);

				return;
			}
		}
		
		public function savePreferences(array $context){
			
		}
		/* ********* INSTALL/UPDATE/UNISTALL ******* */

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function install() {
			return true;

		}

		/**
		 * Creates the table needed for the settings of the field
		 */
		public function update($previousVersion) {
			return true;
		}

		/**
		 *
		 * Drops the table needed for the settings of the field
		 */
		public function uninstall() {
			return true;
		}

	}
	
	
	function gettweets($config){
			require_once(EXTENSIONS . '\twitter_feed\class\class.tweets.php');
			$c = new Tweets;
			$new_values = Symphony::Configuration()->get('twitter-feed');
			$newarray = array();
			foreach($new_values as $value => $val){
				
				if($val == 'false'){
					$val = false;
				}
				elseif($val == 'true' ){
					$val = true;
				}
				if($value == 'order'){
					$newarray[$value] = explode(',',$val);
				}else{
					$newarray[$value] = $val;
				}
			}
			
			return $c->init($newarray);
		}
	
	