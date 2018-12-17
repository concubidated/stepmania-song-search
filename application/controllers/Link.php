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

		$time = 300;
		$url = $this->functions->generate_url($link, 300);
		redirect($url);
	}
}
