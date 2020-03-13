<!-- GeneratePuzzleJSON.php
  * Driver Code  
  * called after let's play button pressed on homepage
  * generates puzzle and solution from python file 
  * session tracking, loads user last game 
  * loads html for game 
-->

<!-- PHP Code: get random puzzle and solutions, start session (cookie) for game, loads user info -->
<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors','1');

# read in the data
$fp=fopen('puzzleData.txt', 'r') or die ("UNABLE TO OPEN FILE!");
# create an array of puzzles by splitting the data by #
$arrayOfPuzzles = explode("#", fread($fp, filesize('puzzleData.txt')));
$arrayOfPuzzles = array_slice($arrayOfPuzzles, 1);

// PUZZLE
if(array_key_exists("puzzle", $_SESSION) && ($_SESSION["puzzle"] !== NULL) && !(isset($_REQUEST["newPuzzle"]))) {
  $randomPuzzleString = $_SESSION["puzzle"];
}
else {
  # choose a random puzzle from the puzzle array
  $randomPuzzleString = $arrayOfPuzzles[array_rand($arrayOfPuzzles)];
}

$_SESSION["puzzle"] = $randomPuzzleString;

// GUESSED WORD LIST
if(array_key_exists("guessedWordList", $_SESSION) && ($_SESSION["guessedWordList"] !== NULL) && !(isset($_REQUEST["newPuzzle"]))) {
  $guessedWordList = $_SESSION["guessedWordList"];
}
else {
  $guessedWordList = [];
}

if(isset($_REQUEST["guessedWordList"]) && $_REQUEST["guessedWordList"] !== "") {
	$guessedWordList = $_REQUEST["guessedWordList"];
}

$_SESSION["guessedWordList"] = $guessedWordList;

// USERNAME
if(array_key_exists("username", $_SESSION)) {
  $username = $_SESSION["username"];
}
else {
  $username = "";
}

$_SESSION["username"] = $username;

#split the array into each piece (each element is either the puzzle letters or the answers)
$randomPuzzleArray = explode("\n", $randomPuzzleString);
# remove the first and last elements of the array
$randomPuzzleArray = array_slice($randomPuzzleArray, 1,-1);

# setup the json object of the puzzle
$puzzleSolutions = array_slice($randomPuzzleArray, 2);
$puzzleJSON = array('puzzleLetters' => $randomPuzzleArray[0], 'keyLetter' => $randomPuzzleArray[1], 'solutions' => $puzzleSolutions);

$data = json_encode($puzzleJSON);
$username = json_encode($username);

?>

<!-- Frontend code for game -->
<!DOCTYPE html>
<html>
  <head>
      <!-- Hive styling -->
      <link rel="stylesheet" type="text/css" href="https://www.cssscript.com/demo/creating-hexagon-buttons-with-pure-css-css3/assets/css/page-style.css">
      <link rel="stylesheet" type="text/css" href="https://www.cssscript.com/demo/creating-hexagon-buttons-with-pure-css-css3/assets/css/hexagons.min.css">
      <link rel="stylesheet" type="text/css" href="index3.css">
      <link href="https://fonts.googleapis.com/css?family=Caladea|Playfair+Display|Spartan&display=swap" rel="stylesheet">
      <script src='https://kit.fontawesome.com/a076d05399.js'></script>
      <link rel="icon" href="favicon.ico">
      <title>Bryn Mawr Bee Player</title>
      <script src="//code.jquery.com/jquery-2.1.1.min.js"></script>
      <script src="path/to/hexagons.min.js"></script>
  </head>
  <body>
    <!-- Header -->
    <h1 class="welcome">Spelling Bee</h1>
    <div class = "gameInfo">
      <div class = "dateInfo">
        <h2 id="date"></h2>
        <form action="", method="post">
          <input type="submit" value="New Puzzle" class = "newPuzzleButton" id="newPuzzleButton"/>
        </form>
      </div>
      <div class = "loginInfo">
        <h2 id = "loginContainer">Welcome, <span id = "login"></span>!</h2>
        <div class = "signoutbutton">
          <form id = "puzzleForm" action="logout.php" method="post">
            <input id="myButton" class = "logoutButton" type="submit" value="Log Out" name="submit"/>
          </form>
          </div>
        </div>
      </div>
    </div>
    <div class = "font">
    <div class="game">
      <!-- Game -->
      <div class="maingame">
          <p id="validation"></p>
            <label>
                <input type="text" name="userinput" id="userinput" autocomplete="off" autofocus onkeypress="return /[a-zA-Z]/i.test(event.key)" spellcheck="false" />
            </label>

            <div class="wordsubmit">
              <input type="button" value="Submit" class = "submit submitButton" id="SubmitButton"/>
            </div>
            <!-- HIVE --> 
          <div class="beehive">
           <div class="row">
              <span id="button3" class = "hb hb-md"></span>
              <span id="button1" class = "hb hb-md"></span>
            </div>
            <div class="row">
              <span id="button2" class = "hb hb-md"></span>
              <span id="button0" class = "hb hb-custom hb-md"></span>
              <span id="button4" class = "hb hb-md"></span>
            </div>
            <div class="row">
              <span id="button5" class = "hb hb-md"></span>
              <span id="button6" class = "hb hb-md"></span>
            </div>
              <br>
              <br>
              <br>
              <br>
        <div class = "submit" id = "reshuffle">
        <button type="submit" class = "reshuffleButton" id = "reshuffleButton" name="submit"><i class='fas fa-sync-alt'></i></button></a>
        </div>
      </div>
      </div>
        <!-- Scores + Guesses -->
        <div class="scoresandlist">
      <div class="scoreandrank">
        <div class="rank">
        <p>Rank</p>
          <div class="rankbox" onclick="displayRankings()">
            <p id="rank" ></p>
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
          <ul id="guessedWords" class="listOfWords"></ul>
        </div>
      </div>
        <!-- Cheat -->
      <form action="cheat.php" method="post" target="_blank">
        <div class="submit" id = "cheatSubmit">
          <input type="hidden" name="cheatLetters" id="cheatInput" />
          <input type="submit" value="Give me the answers!" class = "cheatButton" name="submit"/>
        </div>
          </form>
        </div>
      </div>
    </div>
      <!-- Frontend Javascript: Validation of Guesses -->
    <script>
      <?php
            $guessedWordList = json_encode($guessedWordList);
            echo("
              var puzzleLetters = $data.puzzleLetters;
              var solutions = $data.solutions;
              var keyLetter = $data.keyLetter;
              var guessedWordList = $guessedWordList;
              var username = $username;
              ");
      ?>
      if(typeof guessedWordList === 'string') {
        guessedWordList = guessedWordList.split(",");
      }

      //Global Variables 
      console.log("the guessed words are: " + guessedWordList);
      console.log("puzzle letters are:" + puzzleLetters);
      console.log("key letter is:" + keyLetter);
      console.log("solutions are:" + solutions);
      puzzleLetters = puzzleLetters.replace(keyLetter, "")

      //Load HTML Attributes 
      var userInput = document.getElementById("userinput");
      var submitButton = document.getElementById("SubmitButton");
      var newPuzzleButton = document.getElementById("newPuzzleButton")
      var cheatSubmitButton = document.getElementById("cheatSubmit");
      var cheatInput = document.getElementById("cheatInput");
      var validation = document.getElementById("validation");
      var guessedWords = document.getElementById("guessedWords");
      var reshuffleButton = document.getElementById("reshuffleButton");
      var date = document.getElementById("date");
      var score = document.getElementById("points");
      var rank = document.getElementById("rank");
      var login = document.getElementById("login");

      //Load Hive
      var button0 = document.getElementById("button0");
      var button1 = document.getElementById("button1");
      var button2 = document.getElementById("button2");
      var button3 = document.getElementById("button3");
      var button4 = document.getElementById("button4");
      var button5 = document.getElementById("button5");
      var button6 = document.getElementById("button6");
      
      button0.innerHTML = keyLetter.toUpperCase();
      //Create Hive 
      makeHive(puzzleLetters);
      var maxScore = getMaxScore(solutions);
      date.innerHTML = new Date();
      login.innerHTML = username;

      //Loading Guessed words from session variable g
      var i;
      for(i = 0; i < guessedWordList.length; i++) {
        var li = document.createElement("li");
        li.appendChild(document.createTextNode(guessedWordList[i]));
        guessedWords.appendChild(li);
      }

      // now use the guessed word list from the server to update the score
      var j;
      var total = 0;
      for(j = 0; j < guessedWordList.length; j++) {
          var wordPoints = updateScore(guessedWordList[j]);
          total += parseInt(score.textContent) + wordPoints;
      }
      score.innerHTML = total;
      rank.innerHTML = calculateRanking(total);
      

      //Button Listeners 
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

      newPuzzleButton.onclick = function() {
        generateNewPuzzle();
      }

      userInput.addEventListener("keyup", function(event) {
        //ENTER as submit
        if (event.keyCode === 13) {
          event.preventDefault();
          submitButton.click();
        }
        if (event.keyCode === 32) {
          event.preventDefault();
          reshuffleButton.click();
          var orig = reshuffleButton.style.backgroundColor;
          reshuffleButton.style.backgroundColor = "#DCDCDC";
            setTimeout(function(){
                    reshuffleButton.style.backgroundColor = orig;
              }, 300);
        }
      });

        
      cheatSubmitButton.onclick = function() {
        cheatInput.value = keyLetter + puzzleLetters;
      }

      reshuffleButton.onclick = function() {
        reshuffle(puzzleLetters);
      }
      // HELPER Functions

      // GenerateNewPuzzle: Calls HTTP request for new puzzle  
      function generateNewPuzzle() {
        console.log("generate new puzzle");
        // submit http request for a new puzzle
        var xmlHttp = new XMLHttpRequest();
        var newPuzzle = true;
        xmlHttp.open("GET", "generatePuzzleJSON.php?newPuzzle=" + newPuzzle, true);
        xmlHttp.send();
      }

      //Validate User Guess, Updates HTML 
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
        else { // valid guess
          validation.innerHTML = "Valid guess!";
          guessedWordList.push(userGuess);
          console.log(guessedWordList);
          console.log(guessedWordList.includes(userGuess));
          var li = document.createElement("li");
          li.appendChild(document.createTextNode(userGuess));
          guessedWords.appendChild(li);
          var turnPoints = updateScore(userGuess);
          console.log(turnPoints);
          var totalPoints = parseInt(score.textContent) + turnPoints;
          score.innerHTML = totalPoints;
          rank.innerHTML = calculateRanking(totalPoints);
          console.log("ranking" + calculateRanking(totalPoints));
          console.log("points" + totalPoints); 
          // have javascript send http (a form that has the word list)
          // update the list of guessed words on the server
          var xmlHttp = new XMLHttpRequest();
          xmlHttp.open("GET", "generatePuzzleJSON.php?guessedWordList=" + guessedWordList, true);
          checkEnd = true;
          xmlHttp.send();
          }
              setTimeout(function(){
                validation.innerHTML = "";
              }, 3500)
        if(checkEndGame(guessedWordList)){
          endGame();
        }
      }
      function checkEndGame(guessedWords){
            return guessedWords.length == solutions.length; 
            
      }
      function endGame(){
        alert("You won!! Congrats <3. Click ok to start a new puzzle :)"); 
        newPuzzleButton.click();
      }
      function isPangram(userGuess){
        var guessLetters = new Set(userGuess);
        var puzzleLetterSet = new Set(puzzleLetters);
        for (var a of puzzleLetterSet) if (!guessLetters.has(a)) return false;
        return true;
      }
     
      //Score Ladder Functions
      function displayRankings(){
        var ten = Math.floor(maxScore * 0.05);
        var twenty = Math.floor(maxScore * 0.1);
        var thirty = Math.floor(maxScore * 0.2);
        var fourty = Math.floor(maxScore * 0.4);
        var fifty = Math.floor(maxScore * 0.5);
        var sixty = Math.floor(maxScore * 0.6);
        var seventy = Math.floor(maxScore * 0.7);
        var eighty = Math.floor(maxScore * 0.8);
        var alertString = "The minimum scores to reach each rank for today’s are:\nBeginner: 0\nGood Start: " + ten.toString() + "\nMoving Up:"
                +twenty.toString()
                +"\nGood: "+ thirty.toString()
                + "\nSolid: " + fourty.toString()
                + "\nNice: " + fifty.toString()
                + "\nGreat: " + sixty.toString() 
                + "\nAmazing: " + seventy.toString() 
                + "\nGenius: " + eighty.toString();
        alert(alertString);
      }
      function calculateRanking(totalPoints){
        var ten = Math.floor(maxScore * 0.05);
        var twenty = Math.floor(maxScore * 0.1);
        var thirty = Math.floor(maxScore * 0.2);
        var fourty = Math.floor(maxScore * 0.4);
        var fifty = Math.floor(maxScore * 0.5);
        var sixty = Math.floor(maxScore * 0.6);
        var seventy = Math.floor(maxScore * 0.7);
        var eighty = Math.floor(maxScore * 0.8);
          if ((0 <= totalPoints) && (totalPoints < ten)){
            return "Beginner";
          } else if ((ten <= totalPoints) && (totalPoints < twenty)){
              return "Good Start";
          } else if ((twenty <= totalPoints) &&  (totalPoints < thirty)){
            return "Moving Up";
          } else if ((thirty <= totalPoints) && (totalPoints < fourty)){
            return "Good";
          } else if ((fourty <= totalPoints) && (totalPoints < fifty)){
            return "Solid";
          } else if ((fifty <= totalPoints ) && (totalPoints < sixty)) {
            return "Nice";
          } else if ((sixty <= totalPoints) && (totalPoints < seventy)){
            return "Great";
          } else if ((seventy <= totalPoints) && (totalPoints < eighty)){
            return "Amazing";
          } else if (totalPoints >= eighty){
            return "Genius";
          }
      }
      function getMaxScore(solutions){
        var i;
        var maxScore = 0;
        for(i = 0; i < solutions.length; i++) {
          maxScore += updateScore(solutions[i]);
        }
        return maxScore;
      }
      function updateScore(userGuess) {
        var points = 0;
        if(userGuess.length === 4){
          points = 1;
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
      function makeHive (puzzleLetters){
        button1.innerHTML = puzzleLetters[0].toUpperCase();
        button2.innerHTML = puzzleLetters[1].toUpperCase();
        button3.innerHTML = puzzleLetters[2].toUpperCase();
        button4.innerHTML = puzzleLetters[3].toUpperCase();
        button5.innerHTML = puzzleLetters[4].toUpperCase();
        button6.innerHTML = puzzleLetters[5].toUpperCase();
      }

      function reshuffle(puzzle){
        var shuffledLetters = puzzle.split('').sort(function(){return  0.5-Math.random()}).join('');
        makeHive(shuffledLetters);
        puzzleLetters = shuffledLetters;
      }

      userInput.onblur = function (event) {
      var blurEl = this;
      setTimeout(function() {
          blurEl.focus()
      }, 10);
      }
  </script>
</html>
