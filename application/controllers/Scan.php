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
				if ($type == 'zip' || $type = 'rar'){
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

				//This is much better, only extract sm and dwi files, nothing else.
				//this fixed broken zips with incorrect directories
				if ($zip->open($file) === TRUE) {
    					//$zip->extractTo($scandir);
					for ($i = 0; $i < $zip->numFiles; $i++){
						if(stripos($zip->getNameIndex($i), ".png") || stripos($zip->getNameIndex($i), ".jpg") ||
					  	   	stripos($zip->getNameIndex($i), ".sm") || stripos($zip->getNameIndex($i), ".dwi") ||
							stripos($zip->getNameIndex($i), ".ssc")){
				        		//Packs with sm.old files....
							if(!stripos($zip->getNameIndex($i), ".old")){
								$zip->extractTo($scandir, array($zip->getNameIndex($i)));
								//echo $zip->getNameIndex($i)."\n";
								$foldername = explode('/', $zip->getNameIndex($i))[0];
							}
						}
					}
					//echo $foldername;
					$zip->close();
					//exit();
					//echo $pack['packname'];
					//If ZIP extracted correctly, scan the directory.
					$this->scanDirectory($scandir.$foldername, $pack['id']);
					//exit();
					$sql = "UPDATE Packs SET `scanned` = '1' WHERE id = $pack[id]";
					$query = $this->db->query($sql);

					//Delete extracted directory
					//$this->functions->rrmdir($scandir.$foldername);
					echo "Scanned: ".$pack['packname']."\n";
				} else
    					echo 'Failed to open: '.$pack['packname']."\n";
			//one pack at a time
			exit();
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

		foreach($songs as $songdirtemp){
			$songdir = $folder.'/'.$songdirtemp;
			if(is_dir($songdir) && ($songdirtemp != ".") && ($songdirtemp != "..")){
				$songfiles = scandir($songdir);
				foreach($songfiles as $file){
					$info = pathinfo($songdir."/".$file);
					//If sm file exist and not a hidden file.
					if((strcasecmp(@$info['extension'], 'sm') == '0' || strcasecmp(@$info['extension'],'dwi') == '0' 
					  || strcasecmp(@$info['extension'],'ssc') == '0') && $file{0} != '.'){
						//echo $file."\n";
						//Found a simfile that was utf16, hack to handle this.
                                                //Do not know a better way to do this. Shelling out instead! #bashlyfe
                                                $cmd = "file -ib \"".$songdir."/".$file."\"";
						$format = shell_exec(escapeshellcmd($cmd));
						if(strpos($format,"="))
							$format = explode("=",$format)[1];
						if($format == "utf-16le\n"){
                                                        $cmd = 'iconv -f utf16 -t utf8 "'.$songdir."/".$file.'" > "'.$songdir."/".$file.'"';
							shell_exec($cmd);
                                                        echo $songdir."/".$file.": UTF16, converted to UTF8\n";
                                                }//end utf-16 hackjob fix

						if(strcasecmp($info['extension'], 'dwi') == '0'){
							//print_r($info);
							if(file_exists($songdir."/".$info['filename'].".sm")){
								echo "sm exists, use that instead of .dwi \n";
								continue;
							}
						}
                                                $fh = file($songdir."/".$file);

						//If SM file exists, but is empty, check for dwi file.
						if(filesize($songdir."/".$file) == 0){
							$dwi = $songdir."/".$info['filename'].".dwi";
							if(file_exists($dwi))
								$fh = file($dwi);
							else
								continue;
						}

						//Use python to parse the file first, if it fails, then fallback to the php method below...
						//Set PYTHONPATH becuase I am useing local modules
						$home = posix_getpwuid(posix_getuid())['dir'];
						putenv("PYTHONPATH=$home/local/lib/python2.6/site-packages/");
						$scriptdir = getcwd()."/scripts/";
						$cmd = 'python '.$scriptdir.'sm_parse.py "'.$songdir.'/'.$file.'"';
						$results = shell_exec($cmd);

						if($results != "ERROR"){
							$results = json_decode($results, TRUE);
							$song = $results['song'];
	
							$artist = $this->db->escape($song['artist']);
                                                        $title = $this->db->escape($song['title']);
                                                        $credit = $this->db->escape($song['credit']);
                                                        $hash = sha1_file($songdir."/".$file);
                                                        $subtitle = $this->db->escape($song['subtitle']);
                                                        $titletrans = $this->db->escape($song['titletranslit']);
                                                        $subtitletrans = $this->db->escape($song['subtitletranslit']);
                                                        $bannerhash = "";
							$banner = $song['banner'];
							
                                                        if( !empty($banner) && file_exists($songdir."/".$banner)){							
								$ext = pathinfo($songdir."/".$banner)['extension'];
								$bannerhash = md5($songdir."/".$banner.time()).".".$ext;
								copy($songdir."/".$banner, "static/images/songs/".$bannerhash);
							} else {
								$banner = "";
							}

							$bgchanges = $song['bgchanges'] ? 1 : 0;
							$fgchanges = $song['fgchanges'] ? 1 : 0;

                                                        $sql = "INSERT INTO Songs (`title`, `subtitle`,`titletranslit`,`subtitletranslit`,`artist`,`credit`,`banner`,`bgchanges`,`fgchanges`,`hash`)
                                                                VALUES ($title, $subtitle, $titletrans, $subtitletrans, $artist, $credit,'$bannerhash','$bgchanges','$fgchanges','$hash')";
                                                        
							$query = $this->db->query($sql);
                                                        $songid = $this->db->insert_id();
							//$songid = "1";
                                                        $sql = "INSERT INTO PackSongs (`packid`, `songid`) VALUES ('$packid', '$songid')";
							$query = $this->db->query($sql);
							
							foreach($song['charts'] as $type=>$charts){
								$type = $this->db->escape($type);
								foreach ($charts as $key=>$chart){
									$difficulty = $this->db->escape($key);
									$meter = $this->db->escape($chart['meter']);
									$description = $this->db->escape($chart['description']);
								
									$holds = is_null(@$chart['notes']['holds']) ? "''" : $this->db->escape(@$chart['notes']['holds']);
									$jumps = is_null(@$chart['notes']['jumps']) ? "''" : $this->db->escape(@$chart['notes']['jumps']);
									$mines = is_null(@$chart['notes']['mines']) ? "''" : $this->db->escape(@$chart['notes']['mines']);
									$rolls = is_null(@$chart['notes']['rolls']) ? "''" : $this->db->escape(@$chart['notes']['rolls']);
									$taps = is_null(@$chart['notes']['taps']) ? "''" : $this->db->escape(@$chart['notes']['taps']);
								
									$sql = "INSERT INTO Charts (`songid`,`type`,`difficulty`,`meter`,`holds`,`jumps`,`mines`,`rolls`,`taps`)
                                                                                VALUES ('$songid',$type,$difficulty,$meter,$holds,$jumps,$mines,$rolls,$taps)";
                                                                        //echo "$sql\n";
									$query = $this->db->query($sql);
								}//end for each chart
							}//end foreach type of chart

						} else {
							$artist = preg_grep("/ARTIST/", $fh);
							$title = preg_grep("/TITLE/", $fh);
							$credit = preg_grep("/CREDIT/", $fh);
							$hash = sha1_file($songdir."/".$file);
							$subtitle = "''";
							$titletrans = "''";
							$subtitletrans = "''";
							$bannerhash = "";
							$banner = preg_grep("/BANNER/", $fh);

							foreach($banner as $line){
								$cut = explode(':',$line);
								if(fnmatch("*#BANNER",$cut[0]))
									$banner = explode(';',$cut[1])[0];
								if( !empty($banner) && file_exists($songdir."/".$banner)){
									$ext = pathinfo($songdir."/".$banner)['extension'];
									$bannerhash = md5($songdir."/".$banner.time()).".".$ext;
									echo $bannerhash."\n";
									copy($songdir."/".$banner, "static/images/songs/".$bannerhash);
								} else {
									echo $songdir."/".$banner." missing";
								}
							}//end banner parser

							foreach($artist as $line){
								$cut = explode(':',$line);
								if(fnmatch("*#ARTIST",$cut[0]))
									$artist = $this->db->escape(explode(';',$cut[1])[0]);
							}

							foreach($title as $line){
								//Had to add strip_tags cause yolomania pack is stupid
								$cut = explode(':', strip_tags($line));
								//print_r($cut);
								if(fnmatch("*#TITLE",$cut[0]))
									$title = $this->db->escape(explode(";",$cut[1])[0]);
								if(fnmatch("*#SUBTITLE",$cut[0]))
									$subtitle = $this->db->escape(explode(';',$cut[1])[0]);
								if(fnmatch("*#TITLETRANSLIT",$cut[0]))
									$titletrans = $this->db->escape(explode(';',$cut[1])[0]);
								if(fnmatch("*#SUBTITLETRANSLIT",$cut[0]))
									$subtitletrans = $this->db->escape(explode(';',$cut[1])[0]);
							}//end title(s) parser

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
							//echo $title."\n";
							$sql = "INSERT INTO Songs (`title`, `subtitle`,`titletranslit`,`subtitletranslit`,`artist`,`credit`,`banner`,`hash`)
								VALUES ($title, $subtitle, $titletrans, $subtitletrans, $artist, $credit,'$bannerhash','$hash')";
							$query = $this->db->query($sql);
							$songid = $this->db->insert_id();

							$sql = "INSERT INTO PackSongs (`packid`, `songid`) VALUES ('$packid', '$songid')";
							$query = $this->db->query($sql);
							
						}//end else
						
					}//if sm file

				}//end foreach song
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
