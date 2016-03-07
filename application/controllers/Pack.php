<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Pack extends CI_Controller {



	public function Index(){


		if(is_numeric($this->input->get('id'))){
			$id = $this->input->get('id');
			$sql = "SELECT * FROM Songs as s INNER JOIN PackSongs as ps ON ps.songid = s.id WHERE ps.packid = $id";

                $query = $this->db->query($sql);
                $data['songs'] = $query->result_array();

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


}
