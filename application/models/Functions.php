<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Functions extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
    }




    function rrmdir($dir) {
            if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                            if ($object != "." && $object != "..") {
                                    if (filetype($dir."/".$object) == "dir") $this->rrmdir($dir."/".$object); else unlink($dir."/".$object);
                            }
                    }
            reset($objects);
            rmdir($dir);
            }//end is dir
    }//end rm -r



    function generate_url($file, $time) {

	$file = rawurldecode($file);

	if(is_numeric($time))
		return system("export LANG=C.UTF-8 && s3cmd -c .s3cfg signurl s3://simfiles/\"".$file."\" +".$time);
    }

}
