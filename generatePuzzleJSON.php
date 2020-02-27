<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors','1');

# read in the data
$fp=fopen('puzzleData.txt', 'r') or die ("UNABLE TO OPEN FILE!");
# create an array of puzzles by splitting the data by #
$arrayOfPuzzles = explode("#", fread($fp, filesize('puzzleData.txt')));
$arrayOfPuzzles = array_slice($arrayOfPuzzles, 1);

if(array_key_exists("puzzle", $_SESSION)) {
  $randomPuzzleString = $_SESSION["puzzle"];
}
else {
  # choose a random puzzle from the puzzle array
  $randomPuzzleString = $arrayOfPuzzles[array_rand($arrayOfPuzzles)];
}

if(array_key_exists("score", $_SESSION)) {
  $totalPoints = $_SESSION["score"];
  echo("already has a score!!!!");
}
else {
  $totalPoints = 123988;
  echo("doesn't have the score!");
}


$_SESSION["puzzle"] = $randomPuzzleString;
$_SESSION["score"] = $totalPoints;

#split the array into each piece (each element is either the puzzle letters or the answers)
$randomPuzzleArray = explode("\n", $randomPuzzleString);
# remove the first and last elements of the array
$randomPuzzleArray = array_slice($randomPuzzleArray, 1,-1);

# write this to a file as a JSON object
$jsonObjFile = fopen('puzzleJSON.json', 'w');
$puzzleSolutions = array_slice($randomPuzzleArray, 2);
$puzzleJSON = array('puzzleLetters' => $randomPuzzleArray[0], 'keyLetter' => $randomPuzzleArray[1], 'solutions' => $puzzleSolutions);
fwrite($jsonObjFile, json_encode($puzzleJSON));
fclose($jsonObjFile);

$data = json_encode($puzzleJSON);

?>


<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
	<h1 class="welcome">Spelling Bee</h1> 
  <div class="game">
    <div class="maingame">
        <form class="play">
          <label>
            <input type="text" name="userinput" id="userinput" />
          </label>
          <div class="wordsubmit">
            <input type="button" value="Submit" id="SubmitButton"/>
          </div>
        </form>
        <p id="validation"></p>
        <div class="container">
          <div class="row">
            <button id="button3"></button>
            <button id="button1"></button>
          </div>
          <div class="row">
            <button id="button2"></button>
            <button id="button0"></button>
            <button id="button4"></button>
          </div>
          <div class="row">
            <button id="button5"></button>
            <button id="button6"></button>
          </div>
            <br>
        <div class = "submit" id = "reshuffle">
            <input type="submit" value="Reshuffle" name="submit"/>
        </div>
        </div>

      </div>
      <div class="scoresandlist">
		<div class="scoreandrank">
			<div class="rank">
			<p>Rank</p>
				<div class="rankbox">
					<p id="rank">amazing</p>
				</div>
			</div>
			<div class="score">
				<p>Score</p>
				<div class="scorebox">
					<p id="points">0</p>
				</div>
			</div>
		</div>
		<div class="list">
        <p>List of Guessed Words</p>
			<div class="listbox">
			  <ul id="guessedWords"></ul>
			</div>
		</div>
		<form action="cheat.php" method="post">
			<div class="submit" id = "cheatSubmit">
			  <input type="hidden" name="cheatLetters" id="cheatInput" />
			  <input type="submit" value="Give me the answers!" name="submit"/>
			</div>
      	</form>
      </div>
    </div>

    <script>
      <?php
      echo("
        var puzzleLetters = $data.puzzleLetters;
        var solutions = $data.solutions;
        var keyLetter = $data.keyLetter;
        ");
      ?>
      console.log("puzzle letters are:" + puzzleLetters);
      console.log("key letter is:" + keyLetter);
      console.log("solutions are:" + solutions);
      puzzleLetters = puzzleLetters.replace(keyLetter, "")
      var userInput = document.getElementById("userinput");
      var submitButton = document.getElementById("SubmitButton");
      var cheatSubmitButton = document.getElementById("cheatSubmit");
      var cheatInput = document.getElementById("cheatInput");
      var validation = document.getElementById("validation");
      var guessedWords = document.getElementById("guessedWords");
      var guessedWordList = [];
      var score = document.getElementById("points");

      var button0 = document.getElementById("button0");
      var button1 = document.getElementById("button1");
      var button2 = document.getElementById("button2");
      var button3 = document.getElementById("button3");
      var button4 = document.getElementById("button4");
      var button5 = document.getElementById("button5");
      var button6 = document.getElementById("button6");

      button0.innerHTML = keyLetter.toUpperCase();
      button1.innerHTML = puzzleLetters[0].toUpperCase();
      button2.innerHTML = puzzleLetters[1].toUpperCase();
      button3.innerHTML = puzzleLetters[2].toUpperCase();
      button4.innerHTML = puzzleLetters[3].toUpperCase();
      button5.innerHTML = puzzleLetters[4].toUpperCase();
      button6.innerHTML = puzzleLetters[5].toUpperCase();

      button0.onclick = function(){
        userInput.value = userInput.value + keyLetter.toUpperCase();
       }
      button1.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[0].toUpperCase();
      }
      button2.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[1].toUpperCase();
      }
      button3.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[2].toUpperCase();
      }
      button4.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[3].toUpperCase();
      }
      button5.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[4].toUpperCase();
      }
      button6.onclick = function(){
         userInput.value = userInput.value + puzzleLetters[5].toUpperCase();
      }

      submitButton.onclick = function(){
        var userGuess = userInput.value.toLowerCase();
        console.log("YOU guessed " + userGuess)
        validate(userGuess);
        userInput.value = "";
      }

      cheatSubmitButton.onclick = function() {
        cheatInput.value = keyLetter + puzzleLetters;
      }

      function validate(userGuess) {
        for (var i = 0; i < userGuess.length; i++){
          if(!(puzzleLetters.includes(userGuess.charAt(i))) && userGuess.charAt(i) != keyLetter){
            validation.innerHTML = "Bad letters.";
          }
        }
        if (userGuess.length < 4){
          validation.innerHTML = "Too short. ";
        }
        else if (!userGuess.includes(keyLetter)){
          validation.innerHTML = "Missing key letter. ";
        }
        else if (!solutions.includes(userGuess)) {
          validation.innerHTML = "Not in word list. ";
        }
        else if (guessedWordList.includes(userGuess)){
          validation.innerHTML = "Already guessed. ";
        }
        else {
          validation.innerHTML = "Valid guess!";
          guessedWordList.push(userGuess);
          console.log(guessedWordList);
          console.log(guessedWordList.includes(userGuess));
          var li = document.createElement("li");
          li.appendChild(document.createTextNode(userGuess));
          guessedWords.appendChild(li);
          var turnPoints = updateScore(userGuess);
          console.log(turnPoints);
          //var totalPoints = parseInt(score.textContent) + turnPoints;
          <?php
            echo("
              var totalPoints = $totalPoints;
            ")
        ?>
        score.innerHTML = totalPoints;

        }
        setTimeout(function(){
          validation.innerHTML = "";
        }, 3500)
      }

      function isPangram(userGuess){
        var guessLetters = new Set(userGuess);
        var puzzleLetterSet = new Set(puzzleLetters);
        for (var a of puzzleLetterSet) if (!guessLetters.has(a)) return false;
        return true;
      }

      function updateScore(userGuess) {
        var points = 0;
        if(userGuess.length === 4){
          points = 1;
          console.log("HI");
          console.log(points);
        }
        else if(userGuess.length > 4){
          points = userGuess.length;
          if (isPangram(userGuess)){
            points += 7;
          }
        }
        return points;
      }

    </script>
  </body>
</html>
