<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class DB_Model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
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
	//$sql = "select * from Packs where PackName LIKE '%$search%'";

	$sql = "SELECT p.id, p.packname, p.size_bytes AS size,
                        (SELECT COUNT(*) FROM PackSongs AS ps WHERE ps.packid = p.id) AS songcount
                        FROM Packs AS p
                        INNER JOIN PackSongs AS ps ON ps.packid = p.id
			WHERE p.PackName LIKE '%$search%'
			GROUP BY p.id";
        $query = $this->db->query($sql);

        return($query->result_array());


    }


}
