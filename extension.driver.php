<?php

	/*
	Copyight: Deux Huit Huit 2012
	License: MIT, see the LICENCE file
	*/

	if(!defined("__IN_SYMPHONY__")) die("<h2>Error</h2><p>You cannot directly access this file</p>");

	Class extension_client_logo extends Extension {

		public function getSubscribedDelegates() {
			return array(
				array(
					'page'		=> '/backend/',
					'delegate'	=> 'InitaliseAdminPageHead',
					'callback'	=> 'initaliseAdminPageHead'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'AddCustomPreferenceFieldsets',
					'callback' => 'appendPreferences'
				),
				array(
					'page' => '/system/preferences/',
					'delegate' => 'Save',
					'callback' => 'savePreferences'
				),
			);
		}

		public function uninstall(){
			Administration::Configuration()->remove('client_logo');
			Administration::Configuration()->saveConfig();
		}

		//aggiungo il logo
		public function initaliseAdminPageHead($context) {
			$page = Administration::instance()->Page;
			$pathlogo = General::Sanitize(Administration::Configuration()->get('path', 'client_logo'));
			if($pathlogo != "" and file_exists(DOCROOT.'/workspace/'.$pathlogo)) {
				$extman = Symphony::ExtensionManager();
				$status = $extman->fetchStatus(array('handle' => 'jit_image_manipulation'));
				$fullPathLogo = (in_array(Extension::EXTENSION_ENABLED, $status)) ? URL.'/image/2/0/70/5/'.$pathlogo : URL.'/workspace/'.$pathlogo;

				//controllo l'esistenza e le misure del logo
				$page->addElementToHead(new XMLElement("style", "
					#header h1 a > span {
						display: flex;
						flex-direction: row;
						align-items: center;
						justify-content: flex-start;
					}
					#header h1 a > span:before {
						display: block;
						content: '';
						margin-right: 1.5rem;
						background-image: url(" . $fullPathLogo . ");
						background-repeat: no-repeat;
						background-position: center center;
						background-size: contain;
						width: 3rem;
						height: 3rem;
					}",
					array(
						"type" => "text/css",
						"media" => "screen, projection")
					),
					100012);
			}
		}

		//salvo le preferenze
		public function savePreferences($context){
			$pathlogo = $context['settings']['client_logo']['path'];
			if(file_exists(DOCROOT.'/workspace/'.$pathlogo)) {
				$context['settings']['client_logo'] = array("path" =>$pathlogo);
			}
		}


		//imposto le preferenze
		public function appendPreferences($context){

			$group = new XMLElement('fieldset');
			$group->setAttribute('class', 'settings');
			$group->appendChild(
				new XMLElement('legend', 'Client logo')
			);

			$pathlogo = Widget::Label(__('Client logo path (relative to workspace)'));
			$pathlogo->appendChild(Widget::Input(
				'settings[client_logo][path]', General::Sanitize(Administration::Configuration()->get('path', 'client_logo'))
			));
			$group->appendChild($pathlogo);

			$context['wrapper']->appendChild($group);

		}

	}
