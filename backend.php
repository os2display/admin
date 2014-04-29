<?php

$req = $_GET['req'];

if ($req == 'save') {
  $title = $_POST['title'];
  $file = 'slides/' . $title . '.txt';
  $text = $_POST['text'];
  $text_color = $_POST['textcolor'];
  $text_background_color = $_POST['textbgcolor'];
  $background_color = $_POST['bgcolor'];
  $background_image = $_POST['bgimage'];
  file_put_contents($file, $title, FILE_TEXT | LOCK_EX);
  file_put_contents($file, "\n" . $text, FILE_APPEND | LOCK_EX);
  file_put_contents($file, "\n" . $text_color, FILE_APPEND | LOCK_EX);
  file_put_contents($file, "\n" . $text_background_color, FILE_APPEND | LOCK_EX);
  file_put_contents($file, "\n" . $background_color, FILE_APPEND | LOCK_EX);
  file_put_contents($file, "\n" . $background_image, FILE_APPEND | LOCK_EX);
} elseif ($req == 'load') {

} elseif ($req == 'loadall') {

}