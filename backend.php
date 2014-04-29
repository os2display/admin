<?php
/**
 * Mockup implementation of a backend for indholdskanalen.
 */


$req = $_GET['req'];

if ($req == 'save') {
  $title = $_POST['title'];
  $text = $_POST['text'];
  $text_color = $_POST['textColor'];
  $text_background_color = $_POST['textBackgroundColor'];
  $background_color = $_POST['backgroundColor'];
  $background_image = $_POST['backgroundImage'];

  $file = 'slides/' . $title . '.txt';

  file_put_contents($file, $title . "|" . $text . "|" . $text_color . "|" . $text_background_color . "|" . $background_color . "|" . $background_image, LOCK_EX);
}
elseif ($req == 'load') {
  $title = $_GET['title'];
  $path = "slides/";

  $file = file_get_contents($path . $title . ".txt");
  $lines = explode("|", $file);

  $entry = array(
    "title" => $lines[0],
    "text" => $lines[1],
    "textColor" => $lines[2],
    "textBackgroundColor" => $lines[3],
    "backgroundColor" => $lines[4],
    "backgroundImage" => $lines[5],
  );

  echo json_encode($entry);
}
elseif ($req == 'loadall') {
  $path = "slides/";
  $arr = array();

  if ($handle = opendir($path)) {
    while (false !== ($file = readdir($handle))) {
      if ('.' === $file) continue;
      if ('..' === $file) continue;

      $file = file_get_contents($path . $file);
      $lines = explode("|", $file);

      $entry = array(
        "title" => $lines[0],
        "text" => $lines[1],
        "textColor" => $lines[2],
        "textBackgroundColor" => $lines[3],
        "backgroundColor" => $lines[4],
        "backgroundImage" => $lines[5],
      );

      $arr[] = $entry;
    }
    closedir($handle);
  }

  echo json_encode($arr);
}