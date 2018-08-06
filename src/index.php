<?php

if (array_key_exists('source', $_GET)) {
  header('Content-Type: text/plain; charset=utf-8');
  echo file_get_contents(__FILE__);
  die();
}

session_start();
header('Content-Type: text/plain; charset=utf-8');

function genRandomString() {
  $length = 10;
  $characters = "0123456789abcdefghijklmnopqrstuvwxyz";
  $string = "";

  for ($p = 0; $p < $length; $p++) {
    $string .= $characters[mt_rand(0, strlen($characters)-1)];
  }

  return $string;
}

function makeRandomPath($dir, $ext) {
  do {
    $path = $dir."/".genRandomString().".".$ext;
  } while(file_exists($path));
  return $path;
}

function makeRandomPathFromFilename($dir, $fn) {
  $ext = pathinfo($fn, PATHINFO_EXTENSION);
  return makeRandomPath($dir, $ext);
}

if(array_key_exists("filename", $_POST)) {
  $target_path = makeRandomPathFromFilename("upload", $_POST["filename"]);

  $err=$_FILES['uploadedfile']['error'];
  if($err){
    if($err === 2){
      echo "[-] The uploaded file exceeds MAX_FILE_SIZE\n";
    } else{
      echo "[-] Something went wrong :/\n";
    }
  } else if(filesize($_FILES['uploadedfile']['tmp_name']) > 1000) {
    echo "[-]File is too big\n";
  } else if (!exif_imagetype($_FILES['uploadedfile']['tmp_name'])) {
    echo "[-] File is not an image\n";
  } else {
    if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
      echo "[+] $target_path has been uploaded\n";
    } else{
      echo "[-]There was an error uploading the file, please try again!\n";
    }
  }
}
?>

Endpoints:

 - POST /?uploadedfile=<filecontent>&filename=<filename>

Goal:

 - Content of the `.htflag` file

You can check the source code: /index.php?source
