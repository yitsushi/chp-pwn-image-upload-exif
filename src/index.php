<?php

if (array_key_exists('source', $_GET)) {
  header('Content-Type: text/plain; charset=utf-8');
  echo file_get_contents(__FILE__);
  die();
}

session_start();
header('Content-Type: text/html; charset=utf-8');

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
$messages = [];
$new_profile_image = null;
if(array_key_exists("uploadedfile", $_FILES)) {
  $target_path = makeRandomPathFromFilename("upload", $_FILES['uploadedfile']["name"]);

  $err = $_FILES['uploadedfile']['uploadedfile']['error'];
  if($err){
    if($err === 2){
      $messages[] = "[-] The uploaded file exceeds MAX_FILE_SIZE";
    } else{
      $messages[] = "[-] Something went wrong :/";
    }
  } else if(filesize($_FILES['uploadedfile']['tmp_name']) > 500000) {
    $messages[] = "[-] File is too big";
  } else if (!exif_imagetype($_FILES['uploadedfile']['tmp_name'])) {
    $messages[] = "[-] File is not an image";
  } else {
    list($width, $height) = getimagesize($_FILES['uploadedfile']['tmp_name']);
    if (max($width, $height) > 300) {
      $messages[] = "[-] Maximum width and hight: 500x500";
    } else if(move_uploaded_file($_FILES['uploadedfile']['tmp_name'], $target_path)) {
      $messages[] = "[+] <a href='/{$target_path}'>{$target_path}</a> has been uploaded";
      $new_profile_image = $target_path;
    } else{
      $messages[] = "[-] There was an error uploading the file, please try again!";
    }
  }
}
?><html>
  <head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/js/materialize.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/materialize/1.0.0/css/materialize.min.css">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  </head>
  <body>
    <div class="container">
      <h1 class="header center orange-text">Update profile picture</h1>

      <div class="center">
        <?php foreach ($messages as $message): ?>
        <?php $color = $new_profile_image === null ? "pink" : "purple" ?>
        <blockquote class="<?php echo $color ?>-text accent-1"><?php echo $message ?></blockquote>
        <?php endforeach ?>
      </div>

      <div class="row">
        <div class="center">
          Your goal is to read the content of the <code>.htflag</code> file.
        </div>
        <div class="center">
          You can check the source code: <a href="/index.php?source">/index.php?source</a>
        </div>
        <form class="col offset-s3 s6" target="" method="post" enctype="multipart/form-data">
          <div class="file-field input-field col s12">
            <div class="btn">
              <span>File</span>
              <input name="uploadedfile" type="file">
            </div>
            <div class="file-path-wrapper">
              <input class="file-path validate" type="text">
            </div>
          </div>
          <div class="input-field col s12 center">
            <input class="btn" type="submit" value="Upload">
          </div>
        </form>
      </div>

      <?php if ($new_profile_image !== null): ?>
      <div class="row">
        <div class="col s12 center">
          <img class="responsive-img" src="<?php echo $new_profile_image ?>" />
        </div>
      </div>
      <?php endif ?>
    </div>
  </body>
</html>
