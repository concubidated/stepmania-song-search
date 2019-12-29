<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Link extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
		$data['title'] = "Stepmania Search";	
		$this->load->helper('url');
		$this->load->model('functions');
	}

	public function index($link){



		$headers = $this->getallheaders();
		if(isset($headers['Referer'])){
			if(strpos($headers['Referer'], base_url()) !== False ){
				$time = 300;
				$url = $this->functions->generate_url($link, 300);
				redirect($url);
			}
		}
			$pack = pathinfo(urldecode($link), PATHINFO_FILENAME);
			$url = base_url()."pack/id/".urlencode($pack);
			redirect($url);
	}


	private function getallheaders(){
		$headers = [];
		foreach ($_SERVER as $name => $value) {
			if (substr($name, 0, 5) == 'HTTP_') {
				$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			}
		}
		return $headers;
	}
}
