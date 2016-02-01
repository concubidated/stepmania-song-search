<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Scan extends CI_Controller {

	public function index(){

		/**echo "<pre>";
		echo "This controller will contain the functions to scan the packs.<br>";
		echo "\n1. Generate packlist based on files, organize them in DB as name, type and size and scanned (y/n).<br>";
		echo "2. for archive in db where scanned = no, do<br>";
		echo "\t extract directory, scan each subdirectory, grab information from simfiles, add to db, delete directory<br>";
		echo "3. done";
		echo "</pre>";
		*/
		$this->load->view('scan');

	}


	//This function will update the database packlist with the archives in the 'new' directory.
	//The data added to the database will be, packname, file type, and size. Scanned will be set to FALSE.
	public function updateDatabase(){

		$newdir = NEW_DIRECTORY;
		$newlist = scandir($newdir);

		foreach($newlist as $pack){
			if(strlen($pack) > 2){
				$file = $newdir.$pack;
				$info = pathinfo($file);
				$name = $this->db->escape($info['filename']);
				$type = $this->db->escape(@$info['extension']);
				//If there is an extension then add it to db;
				if ($type != 'NULL'){
					$filesize = filesize($file);
					$sql = "INSERT IGNORE into Packs (`packname`, `filetype`, `size_bytes`) VALUES ($name, $type, '$filesize')";
					//DB Insert, packname is UNIQUE.
					$this->db->query($sql);
				}
			}//end packname greater then 2 charactors. (. and .. directories)

		}
	echo "All 'new' packs added to database and marked for scanning.";
	}



	//This function will extract the directory based on the file type. The extracted directories will be put into
	//the 'scanning' directory.
	public function extractPacks(){

		$this->load->model('functions');

                $newdir = NEW_DIRECTORY;
		$scandir = SCANNING_DIRECTORY;
		$sql = "SELECT * from Packs where `scanned` = '0' AND `filetype` = 'zip'";
		$query = $this->db->query($sql);
		$i=0;
		foreach($query->result_array() as $pack){
			if ($pack['filetype'] == 'zip'){

				$file = $newdir.$pack['packname'].".".$pack['filetype'];
				$zip = new ZipArchive;

				if ($zip->open($file) === TRUE) {
    					$zip->extractTo($scandir);
					//echo $zip->getNameIndex('1');
    					//exit();
					$foldername = explode('/',$zip->getNameIndex('1'))[0];
					$zip->close();
					//echo $pack['packname'];
					//If ZIP extracted correctly, scan the directory.
					$this->scanDirectory($scandir.$foldername, $pack['id']);
					//exit();
					$sql = "UPDATE Packs SET `scanned` = '1' WHERE id = $pack[id]";
					$query = $this->db->query($sql);

					//Delete extracted directory
					$this->functions->rrmdir($scandir.$foldername);
					echo "Scanned: ".$pack['packname']."\n";
				} else
    					echo 'Failed to open: '.$pack['packname'];
			}//end if pack is zip
		}//end foreach pack





	}


	//This function will scan the contents of the directory and update the songs database and the song/pack
	//database. The pack will be moved from the 'new' directory to the 'scanned' directory and Scanned will
	//be marked true.

	//Note: The new and scanned directories should be global variables.
	public function scanDirectory($folder, $packid){

		//echo "<pre>";
		//$folder = strtolower($folder);
		$songs = scandir($folder);

		foreach($songs as $songdir){
			$songdir = $folder.'/'.$songdir;
			if(is_dir($songdir)){
				$songfiles = scandir($songdir);

				foreach($songfiles as $file){
					$info = pathinfo($songdir."/".$file);
					//echo $file."<br>";
					//If sm file exist and not a hidden file.
					if(@$info['extension'] == 'sm' && $file{0} != '.'){

						//Found a simfile that was utf16, hack to handle this.
                                                //Do not know a better way to do this. Shelling out instead! #bashlyfe
                                                $cmd = "file -ib \"".$songdir."/".$file."\"";
						$format = shell_exec($cmd);
						$format = explode("=",$format)[1];
						if($format == "utf-16le\n"){
                                                        $cmd = 'iconv -f utf16 -t utf8 "'.$songdir."/".$file.'" > "'.$songdir."/".$file.'"';
							shell_exec($cmd);
                                                        echo $songdir."/".$file.": UTF16, converted to UTF8\n";
                                                }//end utf-16 hackjob fix

                                                $fh = file($songdir."/".$file);

						//If SM file exists, but is empty, check for dwi file.
						if(filesize($songdir."/".$file) == 0){
							$dwi = $songdir."/".$info['filename'].".dwi";
							if(file_exists($dwi))
								$fh = file($dwi);
							else
								break;
						}

						$artist = preg_grep("/ARTIST/", $fh);
                                                $title = preg_grep("/TITLE/", $fh);
                                                $credit = preg_grep("/CREDIT/", $fh);
						$hash = sha1_file($songdir."/".$file);
						$subtitle = "''";
						$titletrans = "''";
						$subtitletrans = "''";
						foreach($artist as $line){
							$cut = explode(':',$line);
							if(fnmatch("*#ARTIST",$cut[0]))
								$artist = $this->db->escape(explode(';',$cut[1])[0]);
						}

						foreach($title as $line){
							$cut = explode(':', $line);
							//print_r($cut);
							if(fnmatch("*#TITLE",$cut[0]))
								$title = $this->db->escape(explode(";",$cut[1])[0]);
							if(fnmatch("*#SUBTITLE",$cut[0]))
								$subtitle = $this->db->escape(explode(';',$cut[1])[0]);
						        if(fnmatch("*#TITLETRANSLIT",$cut[0]))
                                                                $titletrans = $this->db->escape(explode(';',$cut[1])[0]);
                                                        if(fnmatch("*#SUBTITLETRANSLIT",$cut[0]))
                                                                $subtitletrans = $this->db->escape(explode(';',$cut[1])[0]);
						}

						if(!empty($credit)){
                                                	foreach($credit as $line){
                                                        	$cut = explode(':',$line);
                                                        	if(fnmatch("*#CREDIT",$cut[0]))
                                                                	$credit = $this->db->escape(explode(';',$cut[1])[0]);
                                                	}
						} else
							$credit = "''";

						//Cause sometimes people have typos in simfiles.
						if(is_array($credit))
							$credit = "''";

						//if(is_array($title))
						//	print_r($title);

						$sql = "INSERT INTO Songs (`title`, `subtitle`,`titletranslit`,`subtitletranslit`,`artist`,`credit`, `hash`)
							VALUES ($title, $subtitle, $titletrans, $subtitletrans, $artist, $credit, '$hash')";

						$query = $this->db->query($sql);
						$songid = $this->db->insert_id();

						$sql = "INSERT INTO PackSongs (`packid`, `songid`) VALUES ('$packid', '$songid')";
                                                $query = $this->db->query($sql);

					}//if sm file

				}//end foreach song


				//echo "<pre>";
				//print_r($songfiles);
				//echo "</pre>";
			}

		}//end foreach song
	}//end scan directory

	function rrmdir($dir) {
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
				}
			}
		reset($objects);
		rmdir($dir);
		}//end is dir
	}//end rm -r



}
