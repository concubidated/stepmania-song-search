<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DB_Model extends CI_Model {

	function __construct() {
		// Call the Model constructor
		parent::__construct();
	}




	public function GetAllSongInfo($search){
                $search = urldecode($this->db->escape_str($search));
                $sql = "SELECT s.id, p.id as packid, p.packname, s.title, s.banner, s.artist, s.subtitle, s.credit, s.bgchanges, s.fgchanges, s.hash from Songs as s
			INNER JOIN PackSongs as ps on ps.songid = s.id
			INNER JOIN Packs as p on p.id = ps.packid
			WHERE s.title LIKE '%$search%' ";

                $query = $this->db->query($sql);
		$songs = $query->result_array();
		$songArray = [];
		foreach($songs as $song ){
			$chartArray = [];
			$sql = "SELECT c.type, c.difficulty, c.meter, c.taps, c.jumps, c.mines, c.holds, c.rolls, c.id FROM Charts as c INNER JOIN Songs as s on c.songid =  s.id where s.id = $song[id]";
			$query=$this->db->query($sql);
			foreach($query->result_array() as $chart){
				$chartArray[$chart['id']] = $chart;
			}

			$song['charts'] = $chartArray;
			$songArray[$song['id']] = $song;

		}
                return($songArray);


	}

	public function searchByType($search, $type){

		$type = $this->db->escape_str($type);
		$search = $this->db->escape_str($search);
		$sql = "SELECT p.id, p.packname, s.title, s.banner, s.artist from Songs as s INNER JOIN PackSongs as ps on ps.songid = s.id INNER JOIN Packs as p
			on p.id = ps.packid WHERE s.$type LIKE '%$search%' ";

		$query = $this->db->query($sql);

		return($query->result_array());

	}


	public function searchPackNames($search){
		$search = $this->db->escape_str($search);

		$sql = "SELECT p.id, p.packname, p.size_bytes AS size,
			(SELECT COUNT(*) FROM PackSongs AS ps WHERE ps.packid = p.id) AS songcount
			FROM Packs AS p
			INNER JOIN PackSongs AS ps ON ps.packid = p.id
			WHERE p.PackName LIKE '%$search%'
			GROUP BY p.id";
		$query = $this->db->query($sql);

		return($query->result_array());
	}

	public function getPackIdFromName($id){
		$id = $this->db->escape($id);
		#$sql = "SELECT p.id, p.packname, p.size_bytes AS size, 
		#	(SELECT COUNT(*) FROM PackSongs AS ps WHERE ps.packid = p.id) AS songcount
		#	FROM Packs AS p
		#	INNER JOIN PackSongs AS ps ON ps.packid = p.id
		#	WHERE p.packname = $id
		#	GROUP BY p.id";
		$sql = "SELECT * FROM Packs WHERE packname = $id";
			$query = $this->db->query($sql);
			return $query->row()->id;

	}

	public function packSongInfo($id){
		if($id){
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

			return($songs);
		}
	}

	public function packInfo($id){
		if($id){
			$sql = "SELECT * from Packs Where id = $id";
			$query = $this->db->query($sql);
			return($query->row());

		}
	}

}
