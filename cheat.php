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
echo "

<html>
<head>
  <link rel=\"icon\" href=\"favicon.ico\">
  <title>Bryn Mawr Bee Answers</title>
  <link rel=\"stylesheet\" type=\"text/css\" href=\"cheat.css\">
  <link href=\"https://fonts.googleapis.com/css?family=Caladea|Playfair+Display|Spartan&display=swap\" rel=\"stylesheet\">
</head>
<body>
  <div class=\"all\">
    <h1 class = \"title\">Puzzle Answers for '" . $data1 . "': </h1>
    <div class=\"answers\">

";
while(! feof($file))
{
  $var =  fgets($file);
  echo($var);
  echo("<br>");
}
echo "</div></div></body></html>";

?>
