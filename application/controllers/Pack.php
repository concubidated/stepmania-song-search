<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pack extends CI_Controller {



	public function Index(){


		if(is_numeric($this->input->get('id'))){
			$id = $this->input->get('id');
			$sql = "SELECT *,s.id as id,c.id as chart FROM Songs as s INNER JOIN PackSongs as ps ON ps.songid = s.id
				LEFT JOIN Charts as c on c.songid = s.id WHERE ps.packid = $id";

                $query = $this->db->query($sql);
                $results = $query->result_array();
		$songs = Array();

		foreach($results as $s){
			$songs[$s['id']]['artist'] = $s['artist'];
			$songs[$s['id']]['title'] = $s['title'];
			$songs[$s['id']]['subtitle'] = $s['subtitle'];
			$songs[$s['id']]['titletranslit'] = $s['titletranslit'];
			$songs[$s['id']]['subtitletranslit'] = $s['subtitletranslit'];
			$songs[$s['id']]['credit'] = $s['credit'];
                        $songs[$s['id']]['banner'] = $s['banner'];
                        $songs[$s['id']]['bgchanges'] = $s['bgchanges'];
                        $songs[$s['id']]['fgchanges'] = $s['fgchanges'];
                        $songs[$s['id']]['date'] = $s['date'];

			$songs[$s['id']]['notes'][$s['chart']]['difficulty'] = $s['difficulty'];
			$songs[$s['id']]['notes'][$s['chart']]['type'] = $s['type'];
			$songs[$s['id']]['notes'][$s['chart']]['meter'] = $s['meter'];
                        $songs[$s['id']]['notes'][$s['chart']]['holds'] = $s['holds'];
                        $songs[$s['id']]['notes'][$s['chart']]['jumps'] = $s['jumps'];
                        $songs[$s['id']]['notes'][$s['chart']]['mines'] = $s['mines'];
                        $songs[$s['id']]['notes'][$s['chart']]['rolls'] = $s['rolls'];
                        $songs[$s['id']]['notes'][$s['chart']]['taps'] = $s['taps'];
		}

		$data['songs'] = $songs;

		//echo "<pre>";
		//print_r($songs);
		//echo "</pre>";

		$sql = "SELECT * from Packs Where id = $id";
		$query = $this->db->query($sql);
                $data['pack'] = $query->row();
                $this->load->view('pack_song', $data);

		} else {

		//List all packs and how many songs are in them. Click on a pack to bring up the list of songs.
		$sql = "SELECT p.id, p.packname, p.size_bytes AS size,
			(SELECT COUNT(*) FROM PackSongs AS ps WHERE ps.packid = p.id) AS songcount
			FROM Packs AS p
			INNER JOIN PackSongs AS ps ON ps.packid = p.id
			GROUP BY p.id";

		$query = $this->db->query($sql);
		$data['packs'] = $query->result_array();
		$this->load->view('packs', $data);
		}

	}



	public function Search(){

		if($this->input->get('artist')){

			$artist = $this->db->escape_str($this->input->get('artist'));
                        $sql = "SELECT p.id, p.packname, s.title, s.banner, s.artist from Songs as s INNER JOIN PackSongs as ps on ps.songid = s.id INNER JOIN Packs as p
                                on p.id = ps.packid WHERE s.artist LIKE '%$artist%' ";

                        //echo $sql;

                        $query = $this->db->query($sql);

                        $data['results'] = $query->result_array();
                        $this->load->view('song_search', $data);
		}

		if($this->input->get('q')){

			$title = $this->db->escape_str($this->input->get('q'));
			$sql = "SELECT p.id, p.packname, s.title, s.banner, s.artist from Songs as s INNER JOIN PackSongs as ps on ps.songid = s.id INNER JOIN Packs as p 
				on p.id = ps.packid WHERE s.title LIKE '%$title%' ";

			//echo $sql;

			$query = $this->db->query($sql);

			$data['results'] = $query->result_array();
			$this->load->view('song_search', $data);
		}


	}


	public function Broken(){

		$sql = "SELECT p.id, packname, ps.songid as songid, p.scanned FROM Packs as p LEFT JOIN PackSongs as ps ON ps.packid = p.id where ps.songid is NULL AND p.filetype='zip'";

		$query = $this->db->query($sql);

		echo "<table>";
		foreach($query->result_array() as $pack){
			echo "<tr>";
                        echo "<td>$pack[id]</td>";
			echo "<td>$pack[packname]</td>";
			echo "<td>$pack[scanned]</td>";
			echo "</tr>";

		}
		echo "</table>";
	}


	public function Id($id){
		if(is_numeric($id)){

			$data['songs'] = $this->db_model->packSongInfo($id);
			$data['pack'] = $this->db_model->packInfo($id);
			$this->load->view('template/header', $data);
			$this->load->view('packid', $data);

		}

	}


}

