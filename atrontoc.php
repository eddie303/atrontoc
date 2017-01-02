<?php
$outfile="atrontc.vtc";
//$base=$argv[1];
define('BASE_LEN',count(explode(DIRECTORY_SEPARATOR,realpath($argv[1]))));


function exception_error_handler($errno, $errstr, $errfile, $errline ) {
    throw new ErrorException($errstr, $errno, 0, $errfile, $errline);
}
set_error_handler("exception_error_handler");

try {
  include 'getid3/getid3.php';
} catch (ErrorException $e) {
  die("This script requires getID3. Get it from http://getid3.sourceforge.net/ or on Debian/Ubuntu do sudo apt install php-getid3\n");
}



class AtronToc {
  private $id3;
  public $count=0;

  public function __construct($base, $file) {
    $this->id3=new getID3;
    $f=fopen($file,'w');
    $this->scan_folder(realpath($base), $f, $this->id3);
    fwrite($f,"[End TOC]\n");
    fclose($f);
    echo "\nAdded {$this->count} files to the TOC file.\n";
  }

  private function get_id3($tag_name, $info_array, $whole_array=false) {
    if(!empty($info_array['comments'][$tag_name][0])) {
      if($whole_array) {
        $result="";
        foreach ($info_array['comments'][$tag_name] as $val) {
          $result .= $val.'; ';
        }
        if(!empty($result)) $result = substr($result,0,strlen($result)-2);
        return($result);
      } else {
        return $info_array['comments'][$tag_name][0];
      }
    } else {
      return "--";
    }
  }

  private function scan_folder($fPath, $fHandle, $id3Instance) {
    $files = scandir($fPath);

    foreach($files as $key=>$val) {
      //var_dump($val);
      if(substr($val,0,1)!=".") {
        $file=realpath($fPath.DIRECTORY_SEPARATOR.$val);
        if (is_dir($file)) {
          $this->scan_folder($file, $fHandle, $id3Instance);
        } else {
          $ext = pathinfo($file, PATHINFO_EXTENSION);
          if($ext == "mp3" || $ext == "wma") {
            echo "Adding file: {$file} Extension: {$ext} \n";
            $info=$id3Instance->analyze($file);
            getid3_lib::CopyTagsToComments($info);
            $path_components=explode(DIRECTORY_SEPARATOR,$file);
            $filename = $path_components[count($path_components)-1];
            $dir="";
            for ($i=BASE_LEN;$i<count($path_components)-1; $i++) {
              $dir.=$path_components[$i].'\\';
            }
            $title=$this->get_id3('title', $info);
            $artist=$this->get_id3('artist', $info);
            $album=$this->get_id3('info', $info);
            $genre=$this->get_id3('genre',$info,true);
            $trck=$this->get_id3('track',$info);

            fwrite($fHandle,"SONG
FILE={$filename}
DIR ={$dir}
TIT2={$title}
TALB={$album}
TPE1={$artist}
TCON={$genre}
TRCK={$trck}
TLEN=0
END 
");
            $this->count++;
          }
        }
      }
    }
  }
}

  echo "Turtle Beach Audiotron Table of Contents file generator\nWritten on 2017-01-01 by Eddie303\n\n";
  if (!isset($argv[1])) {
    die("This script needs a path as a parameter, it will then create an Audiotron TOC file on the specified path.\n");
  } else {
    $base = $argv[1];
  }
  $a=new AtronToc($base,$base.DIRECTORY_SEPARATOR.$outfile);

?>
