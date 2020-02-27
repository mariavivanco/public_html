<?php
error_reporting(E_ALL);
ini_set('display_errors','1');

$data1=$_POST['cheatLetters'];
$dictionary = ' /home/strump/public_html/cleanedDictionary.txt';
$answers = shell_exec('/bin/python /home/strump/public_html/cheatPuzzleSolver.py ' . $data1 . $dictionary);

$fp=fopen('answers.txt', 'w') or die ("UNABLE TO OPEN FILE!");
fwrite($fp, $answers);
fclose($fp);

$file = fopen("answers.txt", "r");
echo "<html><body>";
echo "<h1>Puzzle Answers for '" . $data1 . "': </h1>";
while(! feof($file))
{
  $var =  fgets($file);
  echo($var);
  echo("<br>");
}

?>
