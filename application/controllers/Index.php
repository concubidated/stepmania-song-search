<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends CI_Controller {

	public function __construct(){
		parent::__construct();
		

		//Enable Debugging
		//$this->output->enable_profiler(true);
	}

	public function index(){
		$data['title'] = "Stepmania Search";



		if($this->input->post('search')){
			$search = $this->input->post('search');
			$type = $this->input->post('type');
			
			if(!$type) $type="title";


			$this->load->model('db_model');

			if($type == "title")
				$data['results'] = $this->db_model->searchByType($search, $type);
			else if($type == "artist")
				$data['results'] = $this->db_model->searchByType($search, $type);
			else if($type == "packs")
				$data['results'] = $this->db_model->SearchPackNames($search);
			else{
				echo $search." ".$type;
				exit();
			}
       
			$data['type'] = $type;
			$data['search'] = $search;
 
		}


		$this->load->view('template/header', $data);
		$this->load->view('index', $data);

	}


}
