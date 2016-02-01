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


}
