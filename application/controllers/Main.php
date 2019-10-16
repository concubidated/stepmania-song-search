<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Main extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
		$data['title'] = "Stepmania Search";	
		$this->load->helper('url');
		//Enable Debugging
		$this->output->enable_profiler(true);
	}

	public function index(){
		$data['title'] = "Stepmania Search";

		$search_post = urlencode($this->input->post('search'));
		$type_post = $this->input->post('type');

		if(!$type_post) $type_post="title";

		if($search_post)
			redirect("/$type_post/$search_post");


		$numPacks = 20;
		$data['recent_packs'] = $this->db_model->getNewPacks($numPacks);
		$data['random_packs'] = $this->db_model->getRandomPacks($numPacks);

		$this->load->view('template/header', $data);
		$this->load->view('main', $data);
                $this->load->view('template/footer');
	}

	public function search($type, $search){
		$data['title'] = "Stepmania Search - $type";
		if ($type){
			$search = urldecode($search);
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
                $this->load->view('main', $data);
                $this->load->view('template/footer');


	}
}
