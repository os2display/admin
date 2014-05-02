<?php
/**
 * Mockup implementation of a backend for indholdskanalen.
 */

$req = $_GET['req'];
$separator = '|';

if ($req == 'saveslide') {
  $title = $_POST['title'];
  $text = $_POST['text'];
  $text_color = $_POST['textColor'];
  $text_background_color = $_POST['textBackgroundColor'];
  $background_color = $_POST['backgroundColor'];
  $background_image = $_POST['backgroundImage'];
  $id = $_POST['id'];

  if (!is_null($id) && is_numeric($id)) {
    $file = 'slides/' . $id . '.txt';
    file_put_contents($file, $title . $separator . $text . $separator . $text_color . $separator . $text_background_color . $separator . $background_color . $separator . $background_image, LOCK_EX);
  }
  else {
    // Create new
    $nextID = 1 + (int)file_get_contents("slidecounter.txt");
    $file = $backend_dir . 'slides/' . $nextID . '.txt';
    file_put_contents($file, $title . $separator . $text . $separator . $text_color . $separator . $text_background_color . $separator . $background_color . $separator . $background_image, LOCK_EX);
    file_put_contents("slidecounter.txt", $nextID, LOCK_EX);
  }
}
elseif ($req == 'loadslide') {
  $id = $_GET['id'];
  $path = "slides/";

  $file = file_get_contents($path . $id . ".txt");
  $lines = explode($separator, $file);

  $entry = array(
    "title" => $lines[0],
    "text" => $lines[1],
    "textColor" => $lines[2],
    "textBackgroundColor" => $lines[3],
    "backgroundColor" => $lines[4],
    "backgroundImage" => $lines[5],
    "id" => $id
  );

  echo json_encode($entry);
}
elseif ($req == 'loadallslides') {
  $path = "slides/";
  $arr = array();

  if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;

      $id = explode(".txt", $file);
      $id = $id[0];
      $file = file_get_contents($path . $file);
      $lines = explode($separator, $file);

      $entry = array(
        "title" => $lines[0],
        "text" => $lines[1],
        "textColor" => $lines[2],
        "textBackgroundColor" => $lines[3],
        "backgroundColor" => $lines[4],
        "backgroundImage" => $lines[5],
        "id" => $id
      );

      $arr[] = $entry;
    }
    closedir($handle);
  }

  echo json_encode($arr);
}
elseif ($req == 'savechannel') {
  $title = $_POST['title'];
  $orientation = $_POST['orientation'];
  $slides = $_POST['slides'];
  $id = $_POST['id'];

  if (!is_null($id) && is_numeric($id)) {
    $file = 'channels/' . $id . '.txt';
    file_put_contents($file, $title . $separator . orientation . $separator . $slides, LOCK_EX);
  }
  else {
    // Create new
    $nextID = 1 + (int)file_get_contents("channelcounter.txt");
    $file = $backend_dir . 'channels/' . $nextID . '.txt';
    file_put_contents($file, $title . $separator . orientation . $separator . $slides, LOCK_EX);
    file_put_contents("channelcounter.txt", $nextID, LOCK_EX);
  }
}
