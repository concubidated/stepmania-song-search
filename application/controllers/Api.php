<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

	public function __construct(){
		parent::__construct();
		
		$data['title'] = "Stepmania Search";	
		$this->load->helper('url');
		//Enable Debugging
		//$this->output->enable_profiler(true);
	}

	public function song($search){


		$type = 'title';

		if($search){
			$results = $this->db_model->getAllSongInfo($search);

			echo "<pre>";
			print_r(json_encode($results, JSON_PRETTY_PRINT));
			echo "</pre>";
		
		}



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


	}
}
